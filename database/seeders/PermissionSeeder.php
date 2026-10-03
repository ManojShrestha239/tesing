<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            "home",
            "map",
            "notice",
            "order",
            "collection",
            "schedule",
            "chat",
            "complaint",
            "reports",
            "setup",
            "user",
            "dealer",
            "area",
            "product",
            "privacy-policy",
            "miscellaneous",
            "bank",
            "upload-due",
            "distance",
            "denomination",
            "purpose",
            "role",
            "auto-checkout",
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => strtolower($permission)],
                ['guard_name' => 'web']
            );
        }
    }
}
