<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConstructionStage extends Model
{
    protected $table = 'v2_site_construction_stages';
    protected $fillable = ['name', 'material_icon', 'sort_order', 'recommended_followup_days'];
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'material_icon' => 'string',
        'sort_order' => 'integer',
        'recommended_followup_days' => 'integer',
    ];
    public $timestamps = false; // No created_at or updated_at columns

    public function sites(): HasMany
    {
        return $this->hasMany(SiteRegistration::class, 'current_stage_id');
    }

    public function stageLogs(): HasMany
    {
        return $this->hasMany(SiteStageLog::class, 'stage_id');
    }
}
