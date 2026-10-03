<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteType extends Model
{
    protected $table = 'v2_site_types';
    protected $fillable = ['name', 'sort_order'];
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'sort_order' => 'integer',
    ];
    public $timestamps = false; // No created_at or updated_at columns

    public function sites(): HasMany
    {
        return $this->hasMany(SiteRegistrationV2::class, 'site_type_id');
    }
}
