<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateLink extends Model
{
    protected $fillable = ['url', 'banner_path', 'title', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function clicks()
    {
        return $this->hasMany(AffiliateLinkClick::class);
    }
}
