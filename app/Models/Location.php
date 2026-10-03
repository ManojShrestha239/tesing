<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'day', 'coordinates', 'role_id',
    ];

    protected $casts = [
        'coordinates' => 'json'
    ];

    public function user(){
        return $this->belongsTo(Mobileuser::class);
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
