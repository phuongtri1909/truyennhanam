<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryEditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id',
        'user_id',
        'title',
        'slug',
        'description',
        'cover',
        'cover_thumbnail',
        'author_name',
        'categories_data',
        'is_18_plus',
        'has_combo',
        'combo_price',
        'review_note',
        'status',
        'admin_note',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'is_18_plus' => 'boolean',
        'has_combo' => 'boolean',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCategoryIdsAttribute(): array
    {
        $data = json_decode($this->categories_data, true);
        return is_array($data) ? $data : [];
    }
}
