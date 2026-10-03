<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class TadaReportExport implements FromCollection, WithHeadings
{
    protected $rows;

    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return collect($this->rows);
    }

    public function headings(): array
    {
        return [
            'Entry Date',
            'Entry Date (BS)',
            'Officer',
            'Route',
            'Visit Type',
            'KM',
            'Status',
            'Dealer Name',
            'Expense Amount',
            'Expense Types',
            'Total Expenses',
            'Physical Visit',
            'Remarks'
        ];
    }
}
