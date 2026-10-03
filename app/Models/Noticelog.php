<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noticelog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dealer_code',
        'flag',
        'title',
        'notice',
        'date',
    ];
}
