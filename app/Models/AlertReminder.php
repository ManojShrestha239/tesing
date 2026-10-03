<?php

namespace App\Models;

use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertReminder extends Model
{
    use HasFactory, Nepali_Calendar, CustomNepaliCalendarConverter;

    protected $fillable = [
        'dealer_code',
        'date',
        'column_id',
        'table_name',
        'dealer_name',
        'dealer_contact',
        'dealer_address',
        'date',
        'remarks',
        'status',
        'flag',
        'mobileuser_id',
    ];

    protected $casts = [
        // 'date' => $this->custom_AD_to_BS('datetime: Y-m-d'),
        'created_at' => 'datetime: Y-m-d H:i',
        'updated_at' => 'datetime: Y-m-d H:i',
    ];

    public function mobileuser()
    {
        return $this->belongsTo(Mobileuser::class, 'mobileuser_id');
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_code');
    }
    public function reminderpurposes()
    {
        return $this->hasMany(ReminderPurpose::class, 'alertreminder_id');
    }
}
