<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderPurpose extends Model
{
    use HasFactory;

    protected $fillable = [
        'alertreminder_id',
        'purpose_id',
    ];

    protected $casts = [
        'created_at' => 'datetime: Y-m-d H:i',
        'updated_at' => 'datetime: Y-m-d H:i',
    ];

    public function reminderpurposes()
    {
        return $this->belongsTo(AlertReminder::class, 'alertreminder_id');
    }
    public function purpose()
    {
        return $this->belongsTo(Purpose::class, 'purpose_id');
    }
}
