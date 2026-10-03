<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawSize extends Model
{
    use HasFactory;
    protected $fillable = ['unit'];

    public function rawStocks()
    {
        return $this->hasMany(RawStock::class, 'rawsize_id');
    }

    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class, 'rawsize_id');
    }
}
