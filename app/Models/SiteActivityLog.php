<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteActivityLog extends Model
{
    protected $table = 'v2_site_activity_logs';

    // Only 'created_at' exists on this table
    public const UPDATED_AT = null;

    protected $fillable = [
        'site_id',
        'causer_id',
        'web_causer_id',
        'event',
        'description',
        'properties',
    ];

    protected $casts = [
        'id' => 'integer',
        'site_id' => 'integer',
        'causer_id' => 'integer',
        'web_causer_id' => 'integer',
        'event' => 'string',
        'properties' => 'array', // Automatically casts JSON to array
        'description' => 'string',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(SiteRegistrationV2::class, 'site_id');
    }

    public function causer(): BelongsTo
    {
        return $this->belongsTo(Mobileuser::class, 'causer_id');
    }

    public function causerDashboard(): BelongsTo
    {
        return $this->belongsTo(User::class, 'web_causer_id');
    }
}
