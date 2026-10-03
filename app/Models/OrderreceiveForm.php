<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderreceiveForm extends Model
{
    //

    public function complaint(){
        return $this->hasOne(Complaint::class,'complaint_id','id');
    }
}
