<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteStageLog extends Model
{
    protected $table = 'v2_site_stage_logs';
    // Only 'created_at' exists on this table
    public const UPDATED_AT = null;
    protected $fillable = [
        'site_id',
        'stage_id',
        'changed_by_user_id',
        'web_changed_by_id',
        'stage_date',
        'remarks',
    ];

    protected $casts = [
        'id' => 'integer',
        'site_id' => 'integer',
        'stage_id' => 'integer',
        'changed_by_user_id' => 'integer',
        'web_changed_by_id' => 'integer',
        'stage_date' => 'string',
        'remarks' => 'string',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(SiteRegistrationV2::class, 'site_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(ConstructionStage::class, 'stage_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Mobileuser::class, 'changed_by_user_id');
    }

    public function changedByDashboard(): BelongsTo
    {
        return $this->belongsTo(Mobileuser::class, 'web_changed_by_id');
    }
}
