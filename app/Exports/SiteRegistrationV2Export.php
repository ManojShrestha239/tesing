<?php

namespace App\Exports;

use App\Models\SiteRegistrationV2;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiteRegistrationV2Export implements
    FromQuery,
    WithChunkReading,
    WithMapping,
    WithCustomStartCell,
    WithEvents
{
    /** How many sites to read from the DB per batch. */
    private const CHUNK_SIZE = 500;

    /** Cache TTL for the product schema, in seconds. */
    private const SCHEMA_CACHE_TTL = 300; // 5 minutes

    /** Preferred order of product types in the export. */
    private const TYPE_ORDER = [
        'Top Coat',
        'Under Coat',
        'Water Proofing',
        'Enamel',
        'Putty',
    ];

    private array $fixedHeadings = [
        'Site Reg. No.',
        'Date',
        'Paint Contractor Name',
        'Contractor Pass Book No.',
        'Contractor Contact',
        'House Hold Name',
        'Address',
        'House Hold Contact',
        'Floor Of House',
        'Dealer Name',
    ];

    private array $trailingHeadings = [
        'Submitted By',
        'Remarks',
    ];

    /**
     * Grouped structure:
     *   [ type => [ ['subtype' => 'Exterior'|'Interior'|null, 'products' => [names]], ... ] ]
     *
     * @var array<string, array<int, array{subtype: ?string, products: string[]}>>
     */
    private array $productsByType = [];

    /** @var string[] flat list of product names in column order */
    private array $flatProductNames = [];

    /** @var array<string, int> product name → 0-based column index */
    private array $productColumnIndex = [];

    /**
     * Unit per product type. Anything not listed falls back to 'LTR'.
     * @var array<string, string>
     */
    private const TYPE_UNITS = [
        'Putty' => 'KG',
    ];

    /**
     * Return the display unit for a product type.
     */
    private function unitFor(string $type): string
    {
        return self::TYPE_UNITS[$type] ?? 'LTR';
    }

    public function __construct()
    {
        $this->buildProductSchema();
    }

    /* ============================================================
     |  Query — ordered, eager-loaded
     * ============================================================ */
    public function query()
    {
        return SiteRegistrationV2::query()
            ->with([
                'dealer:code,name',
                'contractor:id,name,pass_book_no,contact',
                'registeredBy:id,name',
                'registeredByDashboard:id,name',
                'products:id,name,types',
                'contacts:id,site_id,phone,role',
            ])
            ->orderByDesc('date')
            ->orderByDesc('id');
    }

    public function chunkSize(): int
    {
        return self::CHUNK_SIZE;
    }

    /** Data starts at row 4 — rows 1–3 are the header block. */
    public function startCell(): string
    {
        return 'A4';
    }

    public function map($site): array
    {
        return $this->row($site);
    }

    /* ============================================================
     |  Product schema — cached, grouped, ordered
     * ============================================================ */
    private function buildProductSchema(): void
    {
        $schema = Cache::remember(
            'site_products_schema_v2',
            self::SCHEMA_CACHE_TTL,
            function () {
                return DB::table('v2_site_products')
                    ->select('types', 'name')
                    ->orderBy('types')
                    ->orderBy('name')
                    ->get()
                    ->map(fn($row) => [
                        'type' => $row->types,
                        'name' => $row->name,
                    ])
                    ->all();
            }
        );

        // ---- Bucket products: type → subtype → names ----
        $buckets = [];

        foreach ($schema as $item) {
            $type    = $this->normalizeTypeDisplay($item['type']);
            $name    = $item['name'];
            $subtype = $this->detectSubtype($name); // exterior | interior | general

            $buckets[$type] ??= ['exterior' => [], 'interior' => [], 'general' => []];

            if (! in_array($name, $buckets[$type][$subtype], true)) {
                $buckets[$type][$subtype][] = $name;
            }
        }

        // ---- Order types: preferred first, then alphabetical for extras ----
        $typeKeys = array_keys($buckets);
        usort($typeKeys, function (string $a, string $b): int {
            $ia = array_search($a, self::TYPE_ORDER, true);
            $ib = array_search($b, self::TYPE_ORDER, true);
            $ia = $ia === false ? PHP_INT_MAX : $ia;
            $ib = $ib === false ? PHP_INT_MAX : $ib;

            return $ia <=> $ib ?: strcmp($a, $b);
        });

        // ---- Build final grouped structure ----
        foreach ($typeKeys as $type) {
            $groups = [];

            if (! empty($buckets[$type]['exterior'])) {
                $groups[] = ['subtype' => 'Exterior', 'products' => $buckets[$type]['exterior']];
            }
            if (! empty($buckets[$type]['interior'])) {
                $groups[] = ['subtype' => 'Interior', 'products' => $buckets[$type]['interior']];
            }
            if (! empty($buckets[$type]['general'])) {
                $groups[] = ['subtype' => null, 'products' => $buckets[$type]['general']];
            }

            $this->productsByType[$type] = $groups;
        }

        // ---- Flat list + column index map ----
        foreach ($this->productsByType as $groups) {
            foreach ($groups as $group) {
                foreach ($group['products'] as $name) {
                    $this->productColumnIndex[$name] = count($this->flatProductNames);
                    $this->flatProductNames[]        = $name;
                }
            }
        }
    }

    /**
     * Normalize the type label to its preferred display form.
     * Handles short DB values ('Top', 'Under') and full forms ('Top Coat').
     */
    private function normalizeTypeDisplay(?string $type): string
    {
        $type = ucwords(strtolower(trim((string) $type)));

        return match ($type) {
            '', 'Uncategorized'      => 'Uncategorized',
            'Top'                     => 'Top Coat',
            'Under'                   => 'Under Coat',
            'Waterproofing'           => 'Water Proofing',
            default                   => $type,
        };
    }

    /**
     * Detect subtype from product name.
     * Returns 'exterior', 'interior', or 'general'.
     */
    private function detectSubtype(string $name): string
    {
        $lower = mb_strtolower($name, 'UTF-8');

        if (str_contains($lower, 'exterior')) {
            return 'exterior';
        }
        if (str_contains($lower, 'interior')) {
            return 'interior';
        }

        return 'general';
    }

    /* ============================================================
     |  Header / styling / A4 setup
     * ============================================================ */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $this->writeHeaderRows($sheet);
                $this->applyColumnWidths($sheet);
                $this->styleSheet($sheet);
                $this->setupA4Printing($sheet);
            },
        ];
    }

    /**
     * 3-row header:
     *   Row 1 — type name (merged across its columns)
     *   Row 2 — subtype (Exterior / Interior) — merged across its columns,
     *           or blank+vertical-merged for products without a subtype
     *   Row 3 — product name + unit
     */
    private function writeHeaderRows(Worksheet $sheet): void
    {
        $col = 1;

        // ---- Fixed columns — merged vertically across all 3 header rows ----
        foreach ($this->fixedHeadings as $label) {
            $sheet->setCellValue([$col, 1], $label);
            $sheet->mergeCells([$col, 1, $col, 3]);
            $col++;
        }

        // ---- Product type groups ----
        foreach ($this->productsByType as $type => $groups) {
            $typeColumns = 0;
            foreach ($groups as $group) {
                $typeColumns += count($group['products']);
            }
            if ($typeColumns === 0) {
                continue;
            }

            $typeStart = $col;
            $typeEnd   = $col + $typeColumns - 1;

            // Row 1: type name (merged across the whole type block)
            $sheet->setCellValue([$typeStart, 1], $type);
            if ($typeEnd > $typeStart) {
                $sheet->mergeCells([$typeStart, 1, $typeEnd, 1]);
            }

            // Row 2 + 3: subtypes and product names
            foreach ($groups as $group) {
                $count = count($group['products']);
                if ($count === 0) {
                    continue;
                }

                $subStart = $col;
                $subEnd   = $col + $count - 1;

                // Unit for this type — 'KG' for Putty, 'LTR' for everything else
                $unit = $this->unitFor($type);

                if ($group['subtype'] !== null) {
                    // Row 2: subtype merged across its products
                    $sheet->setCellValue([$subStart, 2], $group['subtype']);
                    if ($subEnd > $subStart) {
                        $sheet->mergeCells([$subStart, 2, $subEnd, 2]);
                    }

                    // Row 3: product names with unit
                    foreach ($group['products'] as $name) {
                        $sheet->setCellValue([$col, 3], $name . ' (' . $unit . ')');
                        $col++;
                    }
                } else {
                    // No subtype — merge row 2–3 vertically per product
                    foreach ($group['products'] as $name) {
                        $sheet->setCellValue([$col, 2], $name . ' (' . $unit . ')');
                        $sheet->mergeCells([$col, 2, $col, 3]);
                        $col++;
                    }
                }
            }
        }

        // ---- Trailing columns — merged vertically ----
        foreach ($this->trailingHeadings as $label) {
            $sheet->setCellValue([$col, 1], $label);
            $sheet->mergeCells([$col, 1, $col, 3]);
            $col++;
        }
    }

    /**
     * Explicit widths — much faster than ShouldAutoSize on large exports.
     */
    private function applyColumnWidths(Worksheet $sheet): void
    {
        // Fixed columns
        $fixedWidths = [18, 12, 22, 20, 18, 22, 30, 18, 12, 24];
        foreach ($fixedWidths as $i => $width) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($width);
        }

        // Product columns
        $productStart = count($this->fixedHeadings) + 1;

        foreach ($this->flatProductNames as $offset => $name) {
            $width = max(14, min(20, mb_strlen($name) * 0.85));
            $sheet->getColumnDimensionByColumn($productStart + $offset)->setWidth($width);
        }

        // Trailing columns
        $trailingStart = $productStart + count($this->flatProductNames);
        foreach ($this->trailingHeadings as $i => $label) {
            $sheet->getColumnDimensionByColumn($trailingStart + $i)->setWidth(22);
        }
    }

    private function styleSheet(Worksheet $sheet): void
    {
        $highestRow      = $sheet->getHighestRow();
        $highestCol      = $sheet->getHighestColumn();
        $highestColIndex = Coordinate::columnIndexFromString($highestCol);

        // ---- Header rows 1–3 ----
        $sheet->getStyle("A1:{$highestCol}3")->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '282D7E'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(26);
        $sheet->getRowDimension(3)->setRowHeight(34);

        if ($highestRow <= 3) {
            return;
        }

        // ---- Data rows start at 4 ----
        $sheet->getStyle("A4:{$highestCol}{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        // Alternating row shading
        for ($r = 4; $r <= $highestRow; $r += 2) {
            $sheet->getStyle("A{$r}:{$highestCol}{$r}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F9FAFB');
        }

        // Center numeric product cells
        $firstProductCol = Coordinate::stringFromColumnIndex(count($this->fixedHeadings) + 1);
        $lastProductCol  = Coordinate::stringFromColumnIndex(
            $highestColIndex - count($this->trailingHeadings)
        );
        $sheet->getStyle("{$firstProductCol}4:{$lastProductCol}{$highestRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function setupA4Printing(Worksheet $sheet): void
    {
        $pageSetup = $sheet->getPageSetup();

        $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);
        $pageSetup->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $pageSetup->setFitToWidth(1);
        $pageSetup->setFitToHeight(0);
        $pageSetup->setFitToPage(true);

        // Repeat the 3-row header on every printed page
        $pageSetup->setRowsToRepeatAtTopByStartAndEnd(1, 3);

        $pageSetup->setHorizontalCentered(true);

        $margins = $sheet->getPageMargins();
        $margins->setTop(0.5);
        $margins->setBottom(0.5);
        $margins->setLeft(0.35);
        $margins->setRight(0.35);
        $margins->setHeader(0.25);
        $margins->setFooter(0.25);

        $sheet->getPageSetup()->setPrintArea(
            'A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow()
        );

        $sheet->freezePane('C4');

        $sheet->getHeaderFooter()
            ->setOddHeader('&L&B Site Registrations&R Printed: &D &T')
            ->setOddFooter('&L' . config('app.name') . '&CPage &P of &N&R&F');
    }

    /* ============================================================
     |  Row builder
     * ============================================================ */
    private function row(SiteRegistrationV2 $site): array
    {
        $householdContact = $site->contacts
            ->sortBy(fn($c) => strtolower($c->role) === 'owner' ? 0 : 1)
            ->first()?->phone;

        $fixed = [
            $site->site_registration_no,
            optional($site->date)->format('Y-m-d') ?? $site->date,
            $site->contractor?->name,
            $site->contractor?->pass_book_no,
            $site->contractor?->contact,
            $site->name,
            $site->address,
            $householdContact,
            $site->floors,
            $site->dealer
                ? $site->dealer->name . ' (' . $site->dealer->code . ')'
                : $site->dealer_code,
        ];

        // Product columns — O(1) per product via the index map
        $productColumns = array_fill(0, count($this->flatProductNames), null);

        foreach ($site->products as $product) {
            $idx = $this->productColumnIndex[$product->name] ?? null;
            if ($idx === null) {
                continue;
            }

            $productColumns[$idx] = ($productColumns[$idx] ?? 0)
                + (float) $product->pivot->quantity;
        }

        $trailing = [
            $site->registeredByDashboard?->name ?? $site->registeredBy?->name,
            'Status: ' . ucwords(str_replace('_', ' ', $site->status)),
        ];

        return array_merge($fixed, $productColumns, $trailing);
    }
}
