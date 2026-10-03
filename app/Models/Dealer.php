<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'main_dealer_code',
        'name',
        'address',
        'latitude',
        'longitude',
        'email',
        'mobile',
        'phone',
        'area_code',
        'order_limit',
        'status',
        'active_status',
        'proprietor_name',
        'pan_vat_no',
        'subarea'
    ];

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';


    public function order()
    {
        return $this->hasMany(Order::class);
    }
    public function collection()
    {
        return $this->hasOne(Payment::class)->orderBy('id', 'desc');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_code');
    }

    public function session()
    {
        return $this->hasMany(Session::class);
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_dealers')
            ->withPivot('assign_qty', 'date')
            ->withTimestamps();
    }

    public function mainDealer()
    {
        return $this->belongsTo(Dealer::class, 'main_dealer_code', 'code');
    }

    public function subDealers()
    {
        return $this->hasMany(Dealer::class, 'main_dealer_code', 'code');
    }

    public function getSubDealersCountAttribute()
    {
        return $this->subDealers()->count();
    }

    public function scopeformaindealer($query)
    {
        return $query->whereNull('main_dealer_code');
    }

    public function scopeforsubdealer($query)
    {
        return $query->whereNotNull('main_dealer_code');
    }

    public function getDealerType()
    {
        return $this->mainDealer === null ? 'Main Dealer' : 'Sub Dealer';
    }
}
