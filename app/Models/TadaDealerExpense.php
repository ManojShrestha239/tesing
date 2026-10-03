<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TadaDealerExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'tada_entry_id',
        'dealer_code',
        'expense_type',
        'amount',
        'is_physical_visit',
        'expenses_authorized_by',
        'authorized_user_id',
        'remarks',
    ];

    protected $casts = [
        'is_physical_visit' => 'boolean',
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

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_code', 'code');
    }

    public function images()
    {
        return $this->hasMany(TadaImage::class, 'tada_dealer_expense_id');
    }
}
