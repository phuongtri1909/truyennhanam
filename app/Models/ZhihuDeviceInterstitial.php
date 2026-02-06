<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZhihuDeviceInterstitial extends Model
{
    protected $fillable = ['device_key', 'last_shown_at'];

    protected $casts = ['last_shown_at' => 'datetime'];
}
