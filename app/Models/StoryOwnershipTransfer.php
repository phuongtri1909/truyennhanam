<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoryOwnershipTransfer extends Model
{
    use HasFactory;

    const TYPE_OWNERSHIP_CHANGE = 'ownership_change';
    const TYPE_CO_OWNER_ADDED = 'co_owner_added';
    const TYPE_CO_OWNER_REMOVED = 'co_owner_removed';

    protected $fillable = [
        'story_id',
        'from_user_id',
        'to_user_id',
        'transferred_by_id',
        'transfer_type',
        'affected_user_id',
        'note',
    ];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by_id');
    }

    public function affectedUser()
    {
        return $this->belongsTo(User::class, 'affected_user_id');
    }

    public function getTransferTypeLabelAttribute(): string
    {
        return match ($this->transfer_type) {
            self::TYPE_OWNERSHIP_CHANGE => 'Chuyển quyền sở hữu',
            self::TYPE_CO_OWNER_ADDED => 'Thêm đồng sở hữu',
            self::TYPE_CO_OWNER_REMOVED => 'Xóa đồng sở hữu',
            default => $this->transfer_type,
        };
    }
}
