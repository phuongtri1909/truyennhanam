<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorySubmit extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id',
        'submitted_note',
        'submitted_at',
        'admin_note',
        'reviewed_at',
        'result',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    const RESULT_PENDING = 'pending';
    const RESULT_APPROVED = 'approved';
    const RESULT_REJECTED = 'rejected';

    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
