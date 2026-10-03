<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TadaActivityLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'tada_entry_id',
        'action_by_user_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function entry()
    {
        return $this->belongsTo(TadaEntry::class, 'tada_entry_id');
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by_user_id');
    }
}
