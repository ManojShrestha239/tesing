<?php

namespace App\Models;

use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SiteRegistrationV2 extends Model
{
    use SoftDeletes, CustomNepaliCalendarConverter;

    protected $table = 'v2_site_registrations';

    protected $fillable = [
        'name',
        'dealer_code',
        'date',
        'contractor_id',
        'registered_by_user_id',
        'web_created_by_id',
        'site_type_id',
        'current_stage_id',
        'address',
        'lat',
        'lng',
        'floors',
        'approx_area_sqft',
        'preferred_brand',
        'competitor_brand_observed',
        'estimated_paint_litres',
        'potential_demand_inr',
        'status',
        'next_followup_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'dealer_code' => 'string',
        'date' => 'string',
        'contractor_id' => 'integer',
        'registered_by_user_id' => 'integer',
        'web_created_by_id' => 'integer',
        'site_type_id' => 'integer',
        'current_stage_id' => 'integer',
        'address' => 'string',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'potential_demand_inr' => 'decimal:2',
        'next_followup_at' => 'string',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    // --- Booted ---
    protected static function booted(): void
    {
        static::creating(function ($model) {
            // 1. Get the dynamic fiscal year (e.g., "82/83")
            // call trait method on the model instance (avoid using $this in static closure)
            $fiscalYear = $model->getFiscalYear();
            $prefix = "DPMF-{$fiscalYear}-";

            // 2. Find the last record created within this specific fiscal year
            // Using DB::transaction and lockForUpdate() is highly recommended
            // if multiple users might create records at the exact same second.
            $lastRecord = static::where('site_registration_no', 'like', "{$prefix}%")
                ->latest('id')
                ->first();

            $nextSequence = 1;

            if ($lastRecord) {
                // 3. Extract the last sequence number and increment it
                $lastSequence = (int)Str::afterLast($lastRecord->site_registration_no, '-');
                $nextSequence = $lastSequence + 1;
            }

            // 4. Format the final ID with 5-digit padding (e.g., 00001)
            $model->site_registration_no = $prefix . str_pad($nextSequence, 5, '0', STR_PAD_LEFT);
        });
    }

    // --- Relationships ---
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(Mobileuser::class, 'registered_by_user_id');
    }

    public function registeredByDashboard(): BelongsTo
    {
        return $this->belongsTo(User::class, 'web_created_by_id');
    }

    public function siteType(): BelongsTo
    {
        return $this->belongsTo(SiteType::class, 'site_type_id');
    }

    public function currentStage(): BelongsTo
    {
        return $this->belongsTo(ConstructionStage::class, 'current_stage_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SiteContact::class, 'site_id');
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(SitePhotoV2::class, 'site_id');
    }

    public function stageLogs(): HasMany
    {
        return $this->hasMany(SiteStageLog::class, 'site_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(SiteFollowUp::class, 'site_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(SiteActivityLog::class, 'site_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(SiteNote::class, 'site_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            SiteProduct::class,
            'v2_site_assgin_product',
            'site_id',
            'site_product_id'
        )->withPivot('quantity');
    }

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class, 'dealer_code', 'code');
    }
}
