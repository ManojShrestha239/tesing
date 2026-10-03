<?php

namespace Database\Seeders;

use App\Models\TadaExpenseAuthorizedUser;
use Illuminate\Database\Seeder;

class TadaExpenseAuthorizedUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Bhim Pandey', 'designation' => 'Director', 'phone' => '9801319661', 'email' => 'bhim@euro.com'],
            ['name' => 'Binayak Pandey', 'designation' => 'Marketing manager', 'phone' => '9801348557', 'email' => 'binayak@euro.com'],
            ['name' => 'Dhruba Raj Chapagain', 'designation' => 'Factory In-charge', 'phone' => '0000000000', 'email' => 'dhruba@euro.com'],
            ['name' => 'Raj kumar Neupane', 'designation' => 'Director', 'phone' => '9705063016', 'email' => 'raj@euro.com'],
            ['name' => 'Rabin Bhusal', 'designation' => 'Director', 'phone' => '9820566189', 'email' => 'rabin@euro.com'],
            ['name' => 'Chetan Basyal', 'designation' => 'Country Head', 'phone' => '9801314494', 'email' => 'chetan@euro.com'],
        ];

        foreach ($users as $user) {
            TadaExpenseAuthorizedUser::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
