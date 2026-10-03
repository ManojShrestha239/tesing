<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderImage extends Model
{
    public function dealer()
  {
    return $this->belongsTo(Dealer::class, 'dealer_code', 'code');
  }

  public function mobileuser()
  {
    return $this->belongsTo(Mobileuser::class, 'mobileuser_id', 'id');
  }
}
