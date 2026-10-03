<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_type', 'dealer_code', 'user_id', 'collection_date', 'amount', 'cheque_num', 'payment_date', 'receipt_num', 'bank_id', 'status',
    ];

    public function dealer(){
        return $this->belongsTo(Dealer::class,'dealer_code');
    }

    public function bank(){
        return $this->belongsTo(Bank::class,'bank_id');
    }

    public function user(){
        return $this->belongsTo(Mobileuser::class,'user_id');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    public function receiptimage(){
        return $this->hasMany(ReceiptImage::class,'collection_id');
    }
    public function denomination(){
        return $this->hasMany(Denominationpoint::class,'collection_id');
    }
}
