<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintDealerUser extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }
    public function dealeruser()
    {
        return $this->belongsTo(Dealer::class, 'dealer_code');
    }
}
