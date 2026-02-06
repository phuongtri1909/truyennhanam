<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateLinkClick extends Model
{
    public $timestamps = false;

    protected $fillable = ['story_id', 'affiliate_link_id', 'device_key', 'ip', 'clicked_at'];

    protected $casts = ['clicked_at' => 'datetime'];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function affiliateLink()
    {
        return $this->belongsTo(AffiliateLink::class);
    }
}
