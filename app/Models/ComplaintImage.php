<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintImage extends Model
{
    protected $fillable = [
        'complaint_id', 'image'
    ];
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
