<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class SitePhotoV2 extends Model
{
    use SoftDeletes;

    protected $table = 'v2_site_photos';

    protected $fillable = [
        'site_id',
        'file_path',
        'photo_type',
    ];

    protected $casts = [
        'id' => 'integer',
        'site_id' => 'integer',
        'file_path' => 'string',
        'photo_type' => 'string',
        'created_at' => 'datetime',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(SiteRegistrationV2::class, 'site_id');
    }

    /**
     * Get the full URL for the file_path.
     *
     * @return string
     */
    public function getFilePathAttribute($value): string
    {
        if (!$value) {
            return '';
        }

        // Check if it's already a full URL
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // If it's a local path, generate a full URL with storage prefix
        return Storage::disk('public')->url($value);
    }
}
