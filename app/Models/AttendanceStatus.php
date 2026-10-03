<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'mobileuser_id',
        'date',
        'time',
        'address',
        'status',
        'km',
        'visit_method',
        'device_model',
        'remarks',
    ];

    public function mobileuser()
    {
        return $this->belongsTo(Mobileuser::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function image()
    {
        return $this->hasOne(AttendanceImage::class, 'attendancestatus_id');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
