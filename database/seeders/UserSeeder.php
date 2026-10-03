<?php

namespace Database\Seeders;

use App\Models\Purpose;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $super = Role::firstOrCreate(
            ['name' => 'Super-Admin'],
            ['guard_name' => 'web']
        );

        $allPermissions = Permission::all();
        $super->syncPermissions($allPermissions);

        $user = User::firstOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Super Admin',
                'phone' => '0000000000',
                'password' => Hash::make('12345678'),
                'user_type' => 'super',
                'app' => 'SOA',
                'status' => 'Active',
            ]
        );

        $user->assignRole($super);

        $users = User::where(['user_type' => 'super', 'app' => 'SOA'])->get();
        foreach ($users as $u) {
            if (!$u->hasRole($super->name)) {
                $u->assignRole($super);
            }
        }

        $purposes = ['Feedback Collection', 'Order', 'Collection'];
        foreach ($purposes as $purpose) {
            Purpose::firstOrCreate(
                ['purpose' => $purpose]
            );
        }
    }
}
