<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitPurpose extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'user_id',
        'purpose_id',
        'date',
        'status',
    ];

    public function purposes()
    {
        return $this->belongsTo(Purpose::class, 'purpose_id');
    }

    public function visit()
    {
        return $this->belongsTo(Session::class, 'visit_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'areacode');
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_code');
    }

    public function user()
    {
        return $this->belongsTo(Mobileuser::class, 'user_id');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
