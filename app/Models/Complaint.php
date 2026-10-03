<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;


    public function dealer()
    {
        return $this->hasOne(ComplaintDealerUser::class, 'complaint_id');
    }
    public function productcomplaints()
    {
        return $this->hasMany(Product_complaint::class, 'complaint_id');
    }
    public function complaintimage()
    {
        return $this->hasMany(ComplaintImage::class, 'complaint_id');
    }
    public function complaintreplies()
    {
        return $this->hasMany(ComplaintReply::class, 'complaint_id');
    }
    public function issuetype()
    {
        return $this->belongsTo(IssueType::class, 'issuetype_id');
    }
    public function dealeruser()
    {
        return $this->hasOne(ComplaintDealerUser::class, 'complaint_id');
    }
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
