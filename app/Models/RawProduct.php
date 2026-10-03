<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'rawmaterial_id'
    ];

    protected $table = 'raw_products';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'rawmaterial_id');
    }
}
