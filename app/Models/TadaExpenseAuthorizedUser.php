<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TadaExpenseAuthorizedUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tada_expense_authorized_users';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'designation',
        'department',
        'status',
    ];

    public function dealerExpenses()
    {
        return $this->hasMany(TadaDealerExpense::class, 'expenses_authorized_by');
    }

    public function otherExpenses()
    {
        return $this->hasMany(TadaOtherExpense::class, 'expenses_authorized_by');
    }
}
