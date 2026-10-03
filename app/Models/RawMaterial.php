<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterial extends Model
{
    use SoftDeletes;

    use HasFactory;
    protected $fillable = [
        'name',
        'total_qty',
        'size_id',
        'available_qty',
        'withheld',
    ];

    protected $table = 'raw_materials';


    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function rawProduct()
    {
        return $this->hasMany(RawProduct::class);
    }
    public function rawInventories()
    {
        return $this->hasMany(RawInventory::class, 'r_id');
    }
    public function rawSize()
    {
        return $this->belongsTo(RawSize::class, 'rawsize_id');
    }

    public function rawStock()
    {
        return $this->hasOne(RawStock::class, 'r_id');
    }
}
