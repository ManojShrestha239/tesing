<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dealer_stock_id',
        'retailer_code',
        'quantity',
        'reason',
        'transaction_type',
        'created_by_type',
        'created_by_id',
    ];

    protected $casts = [
        'dealer_stock_id' => 'integer',
        'retailer_code' => 'string',
        'quantity' => 'integer',
        'reason' => 'string',
        'transaction_type' => 'string',
        'created_by_type' => 'string',
        'created_by_id' => 'integer',
    ];

    public function retailer()
    {
        return $this->belongsTo(Dealer::class, 'retailer_code', 'code')
            ->whereNotNull('main_dealer_code');
    }

    public function dealerStock()
    {
        return $this->belongsTo(DealerStock::class, 'dealer_stock_id');
    }

    public function createdBy()
    {
        return $this->morphTo();
    }
}
