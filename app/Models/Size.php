<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'size',
        'status'
    ];

    public function product()
    {
        return $this->belongsToMany(Product::class, 'product_size', 'size_id', 'product_id');
    }

    //    public function product(){
    //        return $this->belongsToMany(Product::class, 'product_size', 'size_id', 'product_id');
    //    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    public function productstock()
    {
        return $this->hasOne(ProductStock::class, 'size_id');
    }
}
