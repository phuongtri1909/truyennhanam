<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donate extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'amount',
        'fee_percentage',
        'fee_amount',
        'received_amount',
        'message',
    ];

    protected $casts = [
        'amount' => 'integer',
        'fee_percentage' => 'integer',
        'fee_amount' => 'integer',
        'received_amount' => 'integer',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
