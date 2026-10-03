<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedulevisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_code',
        'user_id',
        'purpose',
        'purpose_visit_id',
        'purpose_id',
        'date',
        'is_visit',
        'remarks',
    ];

    public function schedulepurpose()
    {
        return $this->hasMany(SchedulePurpose::class, 'visitschedule_id');
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
