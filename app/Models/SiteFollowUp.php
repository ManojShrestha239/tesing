<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteFollowUp extends Model
{
    use SoftDeletes;

    protected $table = 'v2_site_follow_ups';

    protected $fillable = [
        'site_id',
        'assigned_to_user_id',
        'created_by_user_id',
        'web_changed_by_id',
        'follow_up_type',
        'follow_up_date',
        'status',
        'auto_reminder_enabled',
        'lat',
        'lng',
        'notes',
        'completed_at',
        'rescheduled_to_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'site_id' => 'integer',
        'assigned_to_user_id' => 'integer',
        'created_by_user_id' => 'integer',
        'web_changed_by_id' => 'integer',
        'follow_up_type' => 'string',
        'follow_up_date' => 'string',
        'completed_at' => 'string',
        'auto_reminder_enabled' => 'boolean',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'status' => 'string',
        'notes' => 'string',
        'rescheduled_to_id' => 'integer',
    ];

    protected $hidden = [
        'rescheduled_to_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(SiteRegistrationV2::class, 'site_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Mobileuser::class, 'assigned_to_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Mobileuser::class, 'created_by_user_id');
    }

    public function createdByDashboard(): BelongsTo
    {
        return $this->belongsTo(User::class, 'web_changed_by_id');
    }

    public function rescheduledTo(): BelongsTo
    {
        return $this->belongsTo(SiteFollowUp::class, 'rescheduled_to_id');
    }
}
