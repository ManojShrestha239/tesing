<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return Product::all();
        $data = DB::table('product_size')
            ->join('products', 'products.id', 'product_size.product_id')
            ->join('sizes', 'product_size.size_id', 'sizes.id')
            ->join('categories', 'products.category_id', 'categories.id')
            ->select('product_size.id as product_id', 'products.name as product_name', 'categories.name as Category_name', 'sizes.size as variants', 'product_size.price')
            ->get();
        // dd($data);
        return $data;
    }
    public function headings(): array
    {
        return [
            'product_id',
            'product_name',
            'category_name',
            'variants',
            'price'
        ];
    }
}
