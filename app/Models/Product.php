<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = "products";

    protected  $fillable = [
        'name',
        'category_id',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function size()
    {
        return $this->belongsToMany(Size::class);
    }
    public function challans()
    {
        return $this->hasMany(Challan::class, 'product_id');
    }
    public function rawProduct()
    {
        return $this->hasMany(RawProduct::class);
    }



    //    public function size(){
    //        return $this->belongsToMany(Size::class, 'product_size', 'size_id', 'product_id');
    //    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function productmanuf()
    {
        return $this->hasMany(ProductManufInventory::class, 'product_id');
    }
    public function dealers()
    {
        return $this->belongsToMany(Dealer::class, 'product_dealers')
            ->withPivot('assign_qty', 'date')
            ->withTimestamps();
    }

    public function productstock()
    {
        return $this->hasOne(ProductStock::class, 'product_id');
    }
}
