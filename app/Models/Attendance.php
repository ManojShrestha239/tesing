<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobileuser_id',
        'date',
        'checkin_date',
        'status',
        'address',
        'checkinkm',
        'checkoutkm',
        'totalkm',
        'remarks',
        'visit_method',
        'device_model',
    ];
    public function mobileuser()
    {
        return $this->belongsTo(Mobileuser::class);
    }
    public function attendance_status()
    {
        return $this->hasMany(AttendanceStatus::class, 'attendance_id');
    }

    public function lastStatus()
    {
        return $this->hasOne(AttendanceStatus::class, 'attendance_id')->latestOfMany();
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
