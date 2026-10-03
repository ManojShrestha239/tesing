<?php

namespace App\Exports;

use App\Models\ProductManufacture;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductManufactureExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ProductManufacture::with(['rawInventories.rawmaterial', 'product'])
            ->orderBy('id', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Batch Number',
            'Product Name',
            'Used Rawmaterials',
            'Quantity (In Tons)',
            'No Of Products Manufactured',
            'Manufactured Date',
        ];
    }

    public function map($manufacture): array
    {
        return [
            $manufacture->id, // Batch Number
            $manufacture->product->name ?? '', // Product Name (handle null product)
            $manufacture->rawInventories->map(function ($rawInventory) {
                return $rawInventory->rawmaterial->name ?? '';
            })->implode(', '), // Raw Materials names comma-separated
            $manufacture->rawInventories->map(function ($rawInventory) {
                return $rawInventory->withheld;
            })->implode(', '), // Used quantities comma-separated
            $manufacture->total_qty, // No of Products Manufactured
            $manufacture->date, // Manufactured Date
        ];
    }
}
