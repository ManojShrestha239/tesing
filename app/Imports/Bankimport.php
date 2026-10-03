<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Bank;

class Bankimport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        if (!empty($row['bank'])) {
            $bankName = trim($row['bank']);

            if (!Bank::where('name', $bankName)->exists()) {
                return Bank::create([
                    'name' => $bankName,
                ]);
            }
        }

        return null;
    }

    public function rules(): array
    {
        return [
            'bank' => 'required|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'bank.required' => 'Bank name is required in each row',
        ];
    }
}
