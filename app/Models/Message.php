<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected $fillable = [
        'from',
        'to',
        'message',
        'is_read'
    ];
    public function messageImages()
    {
        return $this->hasMany('App\Models\MessageImages', 'chat_id');
    }
    public function mobileuser()
    {
        return $this->belongsTo('App\Models\Mobileuser', 'from');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'admin');
    }
    public function group()
    {
        return $this->belongsTo('App\Models\MessageGroup');
    }
}
