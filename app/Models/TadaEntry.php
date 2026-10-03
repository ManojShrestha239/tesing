<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Attendance;

class TadaEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'entry_date',
        'route',
        'visit_category',
        'stay_at',
        'is_combined_visit',
        'combined_with',
        'starting_km',
        'ending_km',
        'status',
        'authorized_user_id',
        'remarks',
        'admin_remarks',
        'km_remarks',
    ];

    public function user()
    {
        return $this->belongsTo(Mobileuser::class, 'user_id');
    }

    public function authorizedUser()
    {
        return $this->belongsTo(TadaAuthorizedUser::class, 'authorized_user_id');
    }

    public function visits()
    {
        return $this->hasMany(TadaDealerVisit::class, 'tada_entry_id');
    }

    public function dealerExpenses()
    {
        return $this->hasMany(TadaDealerExpense::class, 'tada_entry_id');
    }

    public function otherExpenses()
    {
        return $this->hasMany(TadaOtherExpense::class, 'tada_entry_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(TadaActivityLog::class, 'tada_entry_id');
    }

    public function images()
    {
        return $this->hasMany(TadaImage::class, 'tada_entry_id');
    }

    public static function attendanceKilometersForUserDate($userId, $entryDate)
    {
        if (empty($userId) || empty($entryDate)) {
            return [
                'starting_km' => null,
                'ending_km' => null,
            ];
        }

        $attendance = Attendance::where('mobileuser_id', $userId)
            ->where('date', $entryDate)
            ->first();

        $checkinStatus = $attendance
            ? AttendanceStatus::where('attendance_id', $attendance->id)
            ->where('status', 'checkin')
            ->orderBy('id')
            ->first()
            : null;
        $checkoutStatus = $attendance
            ? AttendanceStatus::where('attendance_id', $attendance->id)
            ->where('status', 'checkout')
            ->orderByDesc('id')
            ->first()
            : null;

        return [
            'starting_km' => $checkinStatus?->km ?? $attendance?->checkinkm,
            'ending_km' => $checkoutStatus?->km ?? $attendance?->checkoutkm,
        ];
    }
}
