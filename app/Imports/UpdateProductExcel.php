<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductSize;
use App\Models\Size;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UpdateProductExcel implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        if(!empty($row['product_id'])||!empty($row['product_name'])||!empty($row['category_name'])||!empty($row['variants'])||!empty($row['price'])){
            $product = ProductSize::where('id',$row['product_id'])->first();
            if($product){
                $product->price = $row['price'];
                $product->save();
            }
        }else{
            throw ValidationException::withMessages(['email' => 'Column cannot be empty.']);
        }
    }
}
