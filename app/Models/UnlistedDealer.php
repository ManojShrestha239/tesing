<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnlistedDealer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'contact',
        'area_code',
        'subarea',
        'lat',
        'lng',
        'is_active',
    ];

    protected $casts = [
        'name'      => 'string',
        'address'   => 'string',
        'contact'   => 'string',
        'area_code' => 'string',
        'subarea'   => 'string',
        'lat'       => 'decimal:8',
        'lng'       => 'decimal:8',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function scopeactive()
    {
        return $this->where('is_active', true);
    }
}
