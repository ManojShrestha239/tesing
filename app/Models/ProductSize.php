<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;
    protected $table = 'product_size';

    //
    protected $fillable = [
        'product_id', 'size_id', 'price','status'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function size(){
        return $this->belongsTo(Size::class);
    }
    public function subproduct(){
        return $this->belongsTo(Subproduct::class,'subproduct_id');
    }
    public function challans()
    {
        return $this->hasMany(Challan::class,'size_id');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
