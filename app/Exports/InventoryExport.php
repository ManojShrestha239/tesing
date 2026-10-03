<?php

namespace App\Exports;

use App\Models\RawInventory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Fetch the data with relationships
        $rawInventories = RawInventory::with([
            'rawmaterial.size',
            'productManufactured.product'
        ])->get();

        $mapped = $rawInventories->values()->map(function ($inventory, $index) {
            return [
                'SN' => $index + 1,
                'Raw Material Name' => $inventory->rawmaterial->name ?? 'N/A',
                'Date' => $inventory->date,
                'Purchased Quantity' => $inventory->total_qty,
                'Withheld Quantity' => $inventory->withheld,
                'Available Quantity' => $inventory->available,
            ];
        });

        return $mapped;
    }

    /**
     * Define the headings for the Excel export
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'SN',
            'Raw Material Name',
            'Date',
            'Purchased Quantity',
            'Withheld Quantity',
            'Available Quantity',
        ];
    }
}
