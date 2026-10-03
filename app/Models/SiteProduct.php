<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteProduct extends Model
{

    protected $table = 'v2_site_products';

    protected $fillable = [
        'name',
        'types',
        'coverage',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'types' => 'string',
        'coverage' => 'decimal:2',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    //    public function sites(): BelongsToMany
    //    {
    //        return $this->belongsToMany(
    //            SiteRegistrationV2::class,
    //            'v2_site_assgin_product',
    //            'site_product_id',
    //            'site_id'
    //        )->withPivot('quantity');
    //    }
}
