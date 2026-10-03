<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'pass_book_no',
        'contact'
    ];

    protected $casts = [
        'name' => 'string',
        'pass_book_no' => 'string',
        'contact' => 'integer',
        'created_at' => 'date:M d, Y H:i A',
        'updated_at' => 'date:M d, Y H:i A',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
