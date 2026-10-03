<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ageing extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_date', 'area_code', 'report',
    ];

    public function area(){
        return $this->belongsTo(Area::class, 'area_code');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
