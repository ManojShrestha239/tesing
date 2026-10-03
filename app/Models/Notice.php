<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'notice', 'expiry_date',
    ];

    public function user(){
        return $this->belongsTo(Mobileuser::class);
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function noticeimage(){
        return $this->hasMany(NoticeImage::class,'notice_id');
    }
}
