<?php

namespace Database\Seeders;

use App\Models\TadaAuthorizedUser;
use Illuminate\Database\Seeder;

class TadaAuthorizedUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'As per Itinerary',     'designation' => '',           'phone' => '',         'email' => ''],
            ['name' => 'Bhim Pandey',          'designation' => 'Director',     'phone' => '9801319661', 'email' => 'bhim@euro.com'],
            ['name' => 'Binayak Pandey',       'designation' => 'Marketing manager', 'phone' => '9801348557', 'email' => 'binayak@euro.com'],
            ['name' => 'Dhruba Raj Chapagain', 'designation' => 'Factory In-charge', 'phone' => '0000000000', 'email' => 'dhruba@euro.com'],
            ['name' => 'Raj kumar Neupane',    'designation' => 'Director',     'phone' => '9705063016', 'email' => 'raj@euro.com'],
            ['name' => 'Rabin Bhusal',         'designation' => 'Director',     'phone' => '9820566189', 'email' => 'rabin@euro.com'],
            ['name' => 'Chetan Basyal',        'designation' => 'Country Head', 'phone' => '9801314494', 'email' => 'chetan@euro.com'],
        ];

        $created = $updated = $skipped = 0;

        foreach ($users as $row) {
            $existing = $this->findExisting($row);

            if (! $existing) {
                TadaAuthorizedUser::create($row);
                $created++;
                continue;
            }

            // Restore if soft-deleted
            if (method_exists($existing, 'trashed') && $existing->trashed()) {
                $existing->restore();
            }

            $existing->fill($row);

            if (! $existing->isDirty()) {
                $skipped++;
                continue;
            }

            $existing->save();
            $updated++;
        }

        $this->command->info(
            "TADA users seeded — created: {$created}, updated: {$updated}, skipped: {$skipped}"
        );
    }

    private function findExisting(array $row): ?TadaAuthorizedUser
    {
        $query = TadaAuthorizedUser::query();

        // Include soft-deleted rows — a trashed row still occupies the unique phone slot
        if (method_exists(TadaAuthorizedUser::class, 'booted') && in_array(
            \Illuminate\Database\Eloquent\SoftDeletes::class,
            class_uses_recursive(TadaAuthorizedUser::class)
        )) {
            $query->withTrashed();
        }

        if ($row['phone'] !== null && $row['phone'] !== '') {
            return $query->where('phone', $row['phone'])->first();
        }

        // Empty/null phone — match on name
        return $query->where('name', $row['name'])
            ->where(fn($q) => $q->whereNull('phone')->orWhere('phone', ''))
            ->first();
    }
}
