<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductManufacture extends Model
{
    use HasFactory;
    protected $fillable = [
        'p_id',          // Add p_id here
        'total_qty',
        'withdraw_qty',
        'available_qty',
        'status',
        'date',
        'flag',
    ];
    public function rawInventories()
    {
        return $this->hasMany(RawInventory::class, 'pm_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id');
    }
}
