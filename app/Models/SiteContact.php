<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteContact extends Model
{
    use SoftDeletes;

    protected $table = 'v2_site_contacts';
    protected $fillable = [
        'site_id',
        'name',
        'phone',
        'role',
    ];
    protected $casts = [
        'id' => 'integer',
        'site_id' => 'integer',
        'name' => 'string',
        'phone' => 'string',
        'role' => 'string',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(SiteRegistrationV2::class, 'site_id');
    }
}
