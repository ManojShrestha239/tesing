<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintReply extends Model
{
    public function complaint(){
        return $this->belongsTo(Complaint::class,'complaint_id');
    }
    public function dealer(){
        return $this->hasOne(ComplaintReplyDealerUser::class,'complaintreply_id');
    }
    public function user(){
        return $this->hasOne(ComplaintReplyAdminUser::class,'complaintreply_id');
    }
    public function complainimage(){
        return $this->hasOne(ComplaintReplyImage::class,'complaintreply_id');
    }
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
