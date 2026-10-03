<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TadaDealerVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'tada_entry_id',
        'dealer_code',
        'unlisted_dealer_name',
        'is_physical_visit',
        'visit_type',
        'remarks',
    ];

    public function entry()
    {
        return $this->belongsTo(TadaEntry::class, 'tada_entry_id');
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_code', 'code');
    }
}
