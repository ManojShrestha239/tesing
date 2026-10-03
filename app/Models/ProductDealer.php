<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDealer extends Model
{
    use HasFactory;
    protected $fillable = [
        'dealer_id',
        'product_id',
        'assign_qty',
        'date',
        'size_id',
        'invoice_num'
    ];

    protected $table = 'product_dealers';

    // ProductDealer.php
    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id', 'code');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
