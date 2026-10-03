<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_code',
        'user_id',
        'date',
        'status',
        'total_price',
        'remarks',
        'areacode',
        'tint_user',
        'custom_unit_remarks',
        'user_type',
        'collection_commitment_date',
        'payment_commitment_date',
        'committed_amount',
        'delivery_time',
        'delivery_cost',
        'due',
        'last_collection_date',
        'last_collection',
        'discount',
        'vat',
        'is_need_correction',
        'risk_zone',
        'lat_lng',
        'location'
    ];

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_code');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'areacode');
    }

    public function user()
    {
        return $this->belongsTo(Mobileuser::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(Orderitem::class, 'order_id');
    }

    public function challan()
    {
        return $this->hasMany(Challan::class, 'order_id');
    }

    public function dealeruser()
    {
        return $this->belongsTo(DealerUser::class, 'tint_user');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
