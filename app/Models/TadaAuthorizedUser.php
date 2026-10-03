<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TadaAuthorizedUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'designation',
        'department',
        'status',
    ];

    public function entries()
    {
        return $this->hasMany(TadaEntry::class, 'authorized_user_id');
    }

    public function dealerExpenses()
    {
        return $this->hasMany(TadaDealerExpense::class, 'authorized_user_id');
    }

    public function otherExpenses()
    {
        return $this->hasMany(TadaOtherExpense::class, 'authorized_user_id');
    }
}
