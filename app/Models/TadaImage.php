<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TadaImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tada_entry_id',
        'tada_dealer_expense_id',
        'tada_other_expense_id',
        'receipt_image_path',
    ];

    public function entry()
    {
        return $this->belongsTo(TadaEntry::class, 'tada_entry_id');
    }

    public function dealerExpense()
    {
        return $this->belongsTo(TadaDealerExpense::class, 'tada_dealer_expense_id');
    }

    public function otherExpense()
    {
        return $this->belongsTo(TadaOtherExpense::class, 'tada_other_expense_id');
    }
}
