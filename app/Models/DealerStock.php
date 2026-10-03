<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealerStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dealer_code',
        'productsize_id',
        'available_quantity',
        'total_quantity',
        'is_active',
    ];

    protected $casts = [
        'dealer_code' => 'string',
        'productsize_id' => 'integer',
        'available_quantity' => 'integer',
        'total_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class, 'productsize_id');
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'dealer_stock_id');
    }
}
