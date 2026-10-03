<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $table = 'visits';
    protected $fillable = [
        'dealer_code',
        'user_id',
        'purpose_id',
        'start_time',
        'end_time',
        'date',
        'remarks',
        'status',
        'dealer_name',
        'dealer_address',
        'contact',
        'areacode',
        'latitude',
        'longitude',
        'lat_lng',
        'location',
        'is_retailer',
        'is_physical_visit',
    ];

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_code');
    }

    public function user()
    {
        return $this->belongsTo(Mobileuser::class, 'user_id');
    }

    public function purpose()
    {
        return $this->belongsTo(Purpose::class);
    }
    public function visits()
    {
        return $this->hasMany(VisitPurpose::class, 'visit_id');
    }
    public function area()
    {
        return $this->belongsTo(Area::class, 'areacode');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function visitPurposes()
    {
        return $this->hasMany(VisitPurpose::class, 'user_id', 'user_id');
    }

    public function getIntentionsAttribute()
    {
        $ids = $this->parsePurposeIds();

        if (empty($ids)) {
            return collect();
        }

        // Preserve the order stored in the string
        $purposes = Purpose::whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)
            ->map(fn($id) => $purposes->get($id))
            ->filter()
            ->values();
    }

    protected function parsePurposeIds(): array
    {
        if (empty($this->purpose_id)) {
            return [];
        }

        return collect(explode('::', $this->purpose_id))
            ->map(fn($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
