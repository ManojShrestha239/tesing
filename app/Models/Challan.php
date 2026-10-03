<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challan extends Model
{
    use HasFactory;

    public function items(){
        return $this->belongsTo(ProductSize::class,'items_id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }
    public function size(){
        return $this->belongsTo(Size::class,'size_id');
    }
    public function relsize(){
        return $this->belongsTo(Size::class,'size_id');
    }
    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }
    public function subproduct(){
        return $this->belongsTo(Subproduct::class,'subproduct_id');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
