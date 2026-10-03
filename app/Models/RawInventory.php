<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawInventory extends Model
{
    use HasFactory;
    protected $table = 'raw_inventory';

    protected $fillable = [
        'pm_id',
        'r_id',
        'rawsize_id',
        'size_id',
        'total_qty',
        'withheld',
        'available',
        'status',
        'flag',
        'date',
        'batch_number',
    ];

    // Relationship with Rawmaterial
    public function rawmaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'r_id');
    }

    public function productManufactured()
    {
        return $this->belongsTo(ProductManufacture::class, 'pm_id');
    }
}
