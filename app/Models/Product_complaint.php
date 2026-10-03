<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_complaint extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function complaints()
    {
        return $this->belongsTo(complaints::class, 'complaint_id');
    }
}
