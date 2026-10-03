<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TadaOtherExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'tada_entry_id',
        'expense_details',
        'amount',
        'expenses_authorized_by',
        'authorized_user_id',
        'remarks',
    ];

    public function entry()
    {
        return $this->belongsTo(TadaEntry::class, 'tada_entry_id');
    }

    public function authorizedUser()
    {
        return $this->belongsTo(TadaAuthorizedUser::class, 'authorized_user_id');
    }

    public function expenseAuthorizedUser()
    {
        return $this->belongsTo(TadaExpenseAuthorizedUser::class, 'expenses_authorized_by');
    }

    public function images()
    {
        return $this->hasMany(TadaImage::class, 'tada_other_expense_id');
    }
}
