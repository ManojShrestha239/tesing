<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repeatedschedule extends Model
{
    use HasFactory;

    protected $table = "repeateschedules";

    protected $fillable = [
        'user_id','day','area','subarea','dealer_code','visited_dealer','remarks'
    ];
    public function dealervisit(){
        return $this->hasMany(RepeatedDealerVisit::class,'repeateschedule_id ');
    }
    public function user(){
        return $this->belongsTo(Mobileuser::class,'user_id');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
