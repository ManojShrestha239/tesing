<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaMobileuser extends Model
{
    use HasFactory;

    protected $table = "area_mobileuser";

    protected $fillable = [
        'area_code', 'mobileuser_id',
    ];

    public function mobileuser(){
        return $this->belongsTo(Mobileuser::class);
    }

    public function area(){
        return $this->belongsTo(Area::class, 'area_code', 'areacode');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
