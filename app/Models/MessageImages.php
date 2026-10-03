<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageImages extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'image'
    ];

    public function message()
    {
        return $this->belongsTo('App\Models\Message', 'chat_id');
    }
}
