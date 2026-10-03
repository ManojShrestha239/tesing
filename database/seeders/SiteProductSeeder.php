<?php

namespace Database\Seeders;

use App\Models\SiteProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Wall Guard Royal (Exterior)',           'types' => 'Top Coat'],
            ['name' => 'Digital Dura Protect (Exterior)',       'types' => 'Top Coat'],
            ['name' => 'Wall Guard Ext Emulsion (Exterior)',    'types' => 'Top Coat'],
            ['name' => 'De-Guard (Exterior)',                   'types' => 'Top Coat'],
            ['name' => 'Sweet Shine Plus (Interior)',           'types' => 'Top Coat'],
            ['name' => 'Sweet Shine (Interior)',                'types' => 'Top Coat'],
            ['name' => 'Sealing Interior Emulsion (Interior)',  'types' => 'Top Coat'],
            ['name' => 'Sealing Lite (Interior)',               'types' => 'Top Coat'],
            ['name' => 'Wall Sheild Primer (Exterior)',         'types' => 'Under Coat'],
            ['name' => 'Wall Guard Primer (Exterior)',          'types' => 'Under Coat'],
            ['name' => 'Ext Cement Primer (Exterior)',          'types' => 'Under Coat'],
            ['name' => 'Digital Cement Primer (Exterior)',      'types' => 'Under Coat'],
            ['name' => 'Link On Primer (Interior)',             'types' => 'Under Coat'],
            ['name' => 'Digital Damp Guard (Exterior)',         'types' => 'Water Proofing'],
            ['name' => 'Digital Damp Lock (Exterior)',          'types' => 'Water Proofing'],
            ['name' => 'Distmper',                              'types' => 'Enamel'],
            ['name' => 'Digit All',                             'types' => 'Enamel'],
            ['name' => 'Kasturi',                               'types' => 'Enamel'],
            ['name' => 'Wall Safe Putty',                       'types' => 'Putty'],
            ['name' => 'Digital Wall Putty',                    'types' => 'Putty'],
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            DB::table('v2_site_products')->truncate();

            DB::transaction(function () use ($products): void {
                foreach ($products as $product) {

                    // ---- Normalize once ----
                    $name = $this->titleCase($product['name']);

                    $legacyName = $this->titleCase(
                        preg_replace('/\s+\((?:exterior|interior)\)$/i', '', $name)
                    );

                    // ---- Lookup (case-insensitive) ----
                    $existing = DB::table('v2_site_products')
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower($name, 'UTF-8')])
                        ->first();

                    if (! $existing && $legacyName !== $name) {
                        $existing = DB::table('v2_site_products')
                            ->whereRaw('LOWER(name) = ?', [mb_strtolower($legacyName, 'UTF-8')])
                            ->first();
                    }

                    $values = [
                        'name'     => $name,
                        'types'    => $product['types'],
                        'coverage' => 100,
                    ];

                    if ($existing) {
                        DB::table('v2_site_products')
                            ->where('id', $existing->id)
                            ->update($values);
                    } else {
                        DB::table('v2_site_products')->insert($values);
                    }
                }
            });
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Title-case a product name.
     *
     * - Capitalizes the first letter of each word (Unicode-safe).
     * - Fixes hyphenated words ("de-guard" → "De-Guard").
     * - Preserves known acronyms so they don't get mangled (PU, PVC, ISO, etc.).
     */
    private function titleCase(string $value): string
    {
        // 1. Base title-casing
        $value = mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');

        // 2. Ensure the letter after a hyphen is uppercase
        $value = preg_replace_callback('/-([a-z])/u', function ($m) {
            return '-' . mb_strtoupper($m[1], 'UTF-8');
        }, $value);

        // 3. Restore acronyms
        //    Add any acronym your product names use here. Without this,
        //    "PU Adhesive" would become "Pu Adhesive".
        $acronyms = ['PU', 'PVC', 'ISO', 'H2O', 'UV', 'LED', 'HDPE', 'UPVC', 'EXT'];

        foreach ($acronyms as $acronym) {
            $value = preg_replace(
                '/\b' . preg_quote(mb_strtolower($acronym, 'UTF-8'), '/') . '\b/ui',
                $acronym,
                $value
            );
        }

        return $value;
    }
}
