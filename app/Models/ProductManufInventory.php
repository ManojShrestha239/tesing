<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductManufInventory extends Model
{
    use HasFactory;
    protected $table = 'productmanuf_inventories';

    protected $fillable = ['batch_number', 'product_id', 'size_id', 'total_qty', 'date'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
}
