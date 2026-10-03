<?php

namespace App\Exports;

use App\Models\RawProduct;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class RawProductExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $rawproducts = RawProduct::with(['product', 'rawMaterial'])->get();

        return $rawproducts->map(function ($rawproduct, $index) {
            $productName = $rawproduct->product ? $rawproduct->product->name : 'N/A';

            $ids = explode('::', $rawproduct->rawmaterial_id);
            $materials = \App\Models\RawMaterial::whereIn('id', $ids)->pluck('name')->toArray();
            $rawMaterials = implode(', ', $materials);



            return [
                'SN' => $index + 1,
                'Product Name' => $productName,
                'Raw Materials' => $rawMaterials,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'SN',
            'Product Name',
            'Raw Materials',
        ];
    }
}
