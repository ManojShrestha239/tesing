<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'areacode',
        'area',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot',
    ];

    protected $primaryKey = 'areacode';
    public $incrementing = false;
    protected $keyType = 'string';

    public function mobileuser()
    {
        return $this->belongsToMany(Mobileuser::class, 'area_mobileuser', 'area_code', 'mobileuser_id');
    }

    public function subarea()
    {
        return $this->belongsToMany(Subarea::class, 'assignareas', 'areacode', 'subarea_id', 'areacode', 'id');
    }

    public function dealer()
    {
        return $this->hasMany(Dealer::class, 'area_code', 'areacode');
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'area_user', 'user_id', 'areacode');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
