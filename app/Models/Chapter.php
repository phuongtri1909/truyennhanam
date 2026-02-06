<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'number',
        'views',
        'status',
        'story_id',
        'user_id',
        'price',
        'is_free',
        'published_at',
        'scheduled_publish_at',
        'password_encrypted',
        'password_hint',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';


    protected $casts = [
        'is_free' => 'boolean',
        'published_at' => 'datetime',
        'scheduled_publish_at' => 'datetime',
    ];

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    /** Tác giả gốc tạo chương (không đổi khi transfer ownership) */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function purchases()
    {
        return $this->hasMany(ChapterPurchase::class);
    }

    /**
     * Check if a user has purchased this chapter
     */
    public function hasPassword(): bool
    {
        return !empty($this->password_encrypted);
    }

    public function getDecryptedPassword(): ?string
    {
        if (empty($this->password_encrypted)) {
            return null;
        }
        try {
            return Crypt::decryptString($this->password_encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function verifyPassword(string $input): bool
    {
        $stored = $this->getDecryptedPassword();
        return $stored !== null && $stored === $input;
    }

    public function isPurchasedBy($userId)
    {
        if ($this->is_free) {
            return true;
        }
        if ($this->purchases()->where('user_id', $userId)->exists()) {
            return true;
        }
        return $this->story->purchases()->where('user_id', $userId)->exists();
    }
}
