<?php

namespace App\Imports;

use App\Models\Area;
use App\Models\Assignarea;
use App\Models\Dealer;
use App\Models\Subarea;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;

class DealerImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents
{
    private int $insertedCount = 0;
    private array $errors = [];
    private array $allCodes = [];
    private array $allMobiles = [];
    private array $allEmails = [];   // will store only non‑empty emails for uniqueness

    private const MAX_VARCHAR_LENGTH = 191;
    private const REQUIRED_COLUMNS = ['area', 'subarea', 'dealer_code', 'dealer_name'];

    public function collection(Collection $rows): void
    {
        // 1. Column check
        if ($rows->isNotEmpty() && !$this->columnsAreValid($rows->first())) {
            throw ValidationException::withMessages([
                'file' => [
                    'The Excel file is missing required columns. Expected: '
                        . implode(', ', self::REQUIRED_COLUMNS)
                        . '. Found: ' . implode(', ', array_keys($rows->first()->toArray()))
                ]
            ]);
        }

        // 2. Validate every row
        $this->validateRows($rows);

        // 3. Keep valid rows
        $validRows = $rows->filter(function ($row, $index) {
            $rowNumber = $index + 2;
            return empty($this->errors["Row {$rowNumber}"]);
        });

        if ($validRows->isEmpty()) {
            return;
        }

        // 4. Normalise and deduplicate dealer codes within chunk
        $validRows = $validRows->map(function ($row) {
            $row['dealer_code'] = trim($row['dealer_code']);
            return $row;
        })->unique('dealer_code');

        // 5. Extract unique values for bulk checks
        $areaCodes = $validRows->pluck('area')
            ->map(fn($v) => mb_strtolower(trim($v)))
            ->filter()
            ->unique()
            ->values();

        $subareaNames = $validRows->pluck('subarea')
            ->map(fn($v) => mb_strtolower(trim($v)))
            ->filter()
            ->unique()
            ->values();

        $dealerCodes = $validRows->pluck('dealer_code')->unique()->values();

        $mobiles = $validRows->pluck('mobile')
            ->map(fn($v) => trim($v ?? ''))
            ->unique()
            ->values();

        // Only non‑empty emails need a DB uniqueness check
        $emails = $validRows->pluck('email')
            ->map(fn($v) => trim($v ?? ''))
            ->filter()           // remove empty strings
            ->unique()
            ->values();

        // 6. Bulk fetch existing records
        $existingDealerCodes = Dealer::whereIn('code', $dealerCodes)
            ->pluck('code')
            ->flip();

        $existingMobiles = Dealer::whereIn('mobile', $mobiles)
            ->pluck('mobile')
            ->flip();

        $existingEmails = Dealer::whereIn('email', $emails)
            ->pluck('email')
            ->flip();

        $existingAreas = Area::whereIn('areacode', $areaCodes)
            ->get()
            ->keyBy('areacode');

        $existingSubareas = Subarea::whereIn('subarea', $subareaNames)
            ->get()
            ->keyBy('subarea');

        // 7. Process inside transaction
        try {
            DB::transaction(function () use (
                $validRows,
                $existingDealerCodes,
                $existingMobiles,
                $existingEmails,
                $existingAreas,
                $existingSubareas
            ) {
                $dealersToInsert = [];
                $now = now();
                $areasCache = [];
                $subareasCache = [];
                $assignareaCache = [];
                $seenMobiles = [];
                $seenEmails = [];   // only tracks non‑empty emails

                foreach ($validRows as $row) {
                    $dealerCode = $row['dealer_code'];

                    if ($existingDealerCodes->has($dealerCode)) {
                        continue;
                    }

                    $mobile = trim($row['mobile'] ?? '');
                    $email  = trim($row['email'] ?? '');

                    // Mobile uniqueness (empty string is a distinct value)
                    if (isset($seenMobiles[$mobile]) || $existingMobiles->has($mobile)) {
                        continue;
                    }
                    $seenMobiles[$mobile] = true;

                    // Email uniqueness only for non‑empty values
                    if ($email !== '') {
                        if (isset($seenEmails[$email]) || $existingEmails->has($email)) {
                            continue;
                        }
                        $seenEmails[$email] = true;
                    }
                    // Empty emails (→ NULL) are always allowed, no uniqueness check.

                    $areaCode = mb_strtolower(trim($row['area']));
                    $subareaName = mb_strtolower(trim($row['subarea']));

                    // ---- Area ----
                    if (isset($areasCache[$areaCode])) {
                        $area = $areasCache[$areaCode];
                    } elseif ($existingAreas->has($areaCode)) {
                        $area = $existingAreas[$areaCode];
                        $areasCache[$areaCode] = $area;
                    } else {
                        $area = Area::firstOrCreate(
                            ['areacode' => $areaCode],
                            ['area'     => $areaCode]
                        );
                        $areasCache[$areaCode] = $area;
                    }

                    // ---- Subarea ----
                    if (isset($subareasCache[$subareaName])) {
                        $subarea = $subareasCache[$subareaName];
                    } elseif ($existingSubareas->has($subareaName)) {
                        $subarea = $existingSubareas[$subareaName];
                        $subareasCache[$subareaName] = $subarea;
                    } else {
                        $subarea = Subarea::firstOrCreate(['subarea' => $subareaName]);
                        $subareasCache[$subareaName] = $subarea;
                    }

                    // ---- Assignarea ----
                    $assignKey = "{$areaCode}:{$subarea->id}";
                    if (!isset($assignareaCache[$assignKey])) {
                        Assignarea::firstOrCreate([
                            'areacode'   => $areaCode,
                            'subarea_id' => $subarea->id,
                        ]);
                        $assignareaCache[$assignKey] = true;
                    }

                    // ---- Build dealer record ----
                    $dealersToInsert[] = [
                        'code'        => $dealerCode,
                        'name'        => trim($row['dealer_name']),
                        'address'     => trim($row['address'] ?? ''),
                        'mobile'      => $mobile,                     // empty string allowed (NOT NULL)
                        'email'       => $email ?: null,              // empty → null (nullable)
                        'phone'       => trim($row['phone'] ?? ''),
                        'area_code'   => $area->areacode,
                        'subarea'     => $subareaName,
                        'order_limit' => $row['order_limit'] ?? null,
                        'status'      => 'not-approved',
                        'state'       => trim($row['state'] ?? ''),
                        'pan_vat_no'  => trim($row['pan_vat_no'] ?? ''),
                        'series'      => 0,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ];
                }

                if (!empty($dealersToInsert)) {
                    Dealer::insert($dealersToInsert);
                    $this->insertedCount += count($dealersToInsert);
                }
            });
        } catch (\Throwable $e) {
            throw new \RuntimeException("Import failed: " . $e->getMessage(), 0, $e);
        }
    }

    private function validateRows(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $rowErrors = [];

            // Required fields
            foreach (self::REQUIRED_COLUMNS as $column) {
                $value = $row[$column] ?? null;
                if (empty($value) && $value !== '0') {
                    $rowErrors[] = "Missing required field '{$column}'.";
                }
            }

            if (!empty($rowErrors)) {
                $this->errors["Row {$rowNumber}"] = $rowErrors;
                continue;
            }

            $code   = trim($row['dealer_code']);
            $mobile = trim($row['mobile'] ?? '');
            $email  = trim($row['email'] ?? '');

            // Length checks
            $lengthChecks = [
                'dealer_code'  => $code,
                'dealer_name'  => trim($row['dealer_name'] ?? ''),
                'mobile'       => $mobile,
                'email'        => $email,
                'address'      => trim($row['address'] ?? ''),
                'phone'        => trim($row['phone'] ?? ''),
                'state'        => trim($row['state'] ?? ''),
                'pan_vat_no'   => trim($row['pan_vat_no'] ?? ''),
                'area'         => mb_strtolower(trim($row['area'])),
                'subarea'      => mb_strtolower(trim($row['subarea'])),
            ];

            foreach ($lengthChecks as $field => $val) {
                if (mb_strlen($val) > self::MAX_VARCHAR_LENGTH) {
                    $rowErrors[] = "Field '{$field}' exceeds maximum length of " . self::MAX_VARCHAR_LENGTH . " characters.";
                }
            }

            // File‑level uniqueness – dealer code
            if (isset($this->allCodes[$code])) {
                $rowErrors[] = "Duplicate dealer_code '{$code}' – already present at row {$this->allCodes[$code]}.";
            } else {
                $this->allCodes[$code] = $rowNumber;
            }

            // Mobile uniqueness (empty string is distinct)
            if ($mobile !== '') {
                if (isset($this->allMobiles[$mobile])) {
                    $rowErrors[] = "Duplicate mobile '{$mobile}' – already used at row {$this->allMobiles[$mobile]}.";
                } else {
                    $this->allMobiles[$mobile] = $rowNumber;
                }
            } else {
                if (isset($this->allMobiles[''])) {
                    $rowErrors[] = "Duplicate empty mobile – only one row may have no mobile. Already used at row {$this->allMobiles['']}.";
                } else {
                    $this->allMobiles[''] = $rowNumber;
                }
            }

            // Email uniqueness – only for non‑empty values
            if ($email !== '') {
                if (isset($this->allEmails[$email])) {
                    $rowErrors[] = "Duplicate email '{$email}' – already used at row {$this->allEmails[$email]}.";
                } else {
                    $this->allEmails[$email] = $rowNumber;
                }
            }
            // Empty emails are ignored – multiple allowed

            if (!empty($rowErrors)) {
                $this->errors["Row {$rowNumber}"] = $rowErrors;
            }
        }
    }

    private function columnsAreValid($firstRow): bool
    {
        $keys = array_keys($firstRow->toArray());
        return empty(array_diff(self::REQUIRED_COLUMNS, $keys));
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                if (!empty($this->errors)) {
                    throw ValidationException::withMessages($this->errors);
                }
            },
        ];
    }

    public function chunkSize(): int
    {
        return config('dealer_import.chunk_size', 500);
    }

    public function getInsertedCount(): int
    {
        return $this->insertedCount;
    }
}
