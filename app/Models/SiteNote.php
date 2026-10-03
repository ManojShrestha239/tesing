<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteNote extends Model
{
    protected $table = 'v2_site_notes';

    protected $fillable = [
        'site_id',
        'user_id',
        'web_user_id',
        'note',
    ];
    protected $casts = [
        'id' => 'integer',
        'site_id' => 'integer',
        'user_id' => 'integer',
        'web_user_id' => 'integer',
        'note' => 'string',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(SiteRegistrationV2::class, 'site_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userDashboard(): BelongsTo
    {
        return $this->belongsTo(User::class, 'web_user_id');
    }
}
