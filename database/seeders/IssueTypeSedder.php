<?php

namespace Database\Seeders;

use App\Models\IssueType;
use Illuminate\Database\Seeder;

class IssueTypeSedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $issueTypes = [
            "Account",
            "Product",
            "Machine",
            "Other"
        ];

        foreach ($issueTypes as $issueType) {
            $existingIssueType = IssueType::where('type', $issueType)->first();
            if (!$existingIssueType) {
                IssueType::create(['type' => $issueType]);
            }
        }

        $purposes = ['Feedback Collection', 'Order', 'Collection'];

        foreach ($purposes as $purpose) {
            $existingPurpose = \App\Models\Purpose::where('purpose', $purpose)->first();
            if (!$existingPurpose) {
                \App\Models\Purpose::create(['purpose' => $purpose]);
            }
        }

        $existingBank = \App\Models\Bank::where('name', 'cash')->first();
        if (!$existingBank) {
            \App\Models\Bank::create(['name' => 'cash']);
        }
    }
}
