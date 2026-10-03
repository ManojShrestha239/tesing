<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawStock extends Model
{
    use HasFactory;
    protected $fillable = [
        'r_id',
        'total_qty',
        'withdraw',
        'available',
        'batch_number',
        'rawsize_id',
    ];
    protected $table = 'raw_stock';


    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'r_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function rawSize()
    {
        return $this->belongsTo(RawSize::class, 'rawsize_id');
    }
}
