<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulePurpose extends Model
{
    use HasFactory;

    public function purpose(){
        return $this->belongsTo(Purpose::class,'purpose_id');
    }
    public function schedulevisit(){
        return $this->belongsTo(Schedulevisit::class,'purpose_id');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
