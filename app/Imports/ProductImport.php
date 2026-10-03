<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductSize;
use App\Models\Size;

class ProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['price'])) {
            $price = 0;
        } else {
            $price = $row['price'];
        }
        $cat = Category::where('name', $row['category_name'])->first();
        if (!$cat) {
            $cat = Category::create([
                'name' => $row['category_name'],
            ]);
        }
        $product = Product::where(['category_id' => $cat->id, 'name' => $row['product_name']])->first();
        if (!$product) {
            $product = Product::create([
                'name' => $row['product_name'],
                'category_id' => $cat->id,
            ]);
        }

        $size = Size::where('size', $row['variants'])->first();
        if (!$size) {
            $size = Size::create([
                'size' => $row['variants'],
            ]);
        }

        $final = ProductSize::where('product_id', $product->id)->where('size_id', $size->id)->first();
        if (!$final) {
            $final = ProductSize::create([
                'product_id' => $product->id,
                'size_id' => $size->id,
                'price' => $price,
                'status' => 1,
            ]);
        }
    }
}
