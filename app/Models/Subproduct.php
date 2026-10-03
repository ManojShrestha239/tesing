<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subproduct extends Model
{
    use HasFactory;
    protected $table = 'subproducts';

    protected $fillable = ['subproduct'];

    public function product_size(){
        return $this->hasMany(ProductSize::class,'subproduct_id');
    }

    public function challans()
    {
        return $this->hasMany(Challan::class,'subproduct_id');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
