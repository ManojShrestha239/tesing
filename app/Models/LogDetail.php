<?php

namespace App\Models;

use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogDetail extends Model
{
    use HasFactory, CustomNepaliCalendarConverter;

    protected $table = 'logdetails';

    protected $fillable = [
        'ordercoll_id',
        'flag',
        'user_id',
        'tint_user',
        'cheque_no',
        'date',
        'admin',
        'mo_user',
        'status',
        'previous_status',
        'previous_due_amount',
        'previous_lastyearsales',
        'amount',
        'update_status',
        'update_due_amount',
        'update_lastyearsales',
        'remarks',
        'created_at',
        'updated_at'
    ];

    protected $appends = ['nepali_created_at'];

    public function collection()
    {
        return $this->belongsTo(Payment::class, 'ordercoll_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'ordercoll_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'admin');
    }

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin');
    }

    public function getNepaliCreatedAtAttribute()
    {
        if (!$this->created_at) {
            return null;
        }
        $created_at = explode(' ', (string)$this->created_at);
        return $this->custom_AD_to_BS($created_at[0]) . ' ' . $created_at[1];
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
