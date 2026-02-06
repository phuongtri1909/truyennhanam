<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebFeedback extends Model
{
    use HasFactory;
    protected $table = 'web_feedbacks';

    const INTENSITY_LOW = 'low';
    const INTENSITY_MEDIUM = 'medium';
    const INTENSITY_HIGH = 'high';
    const INTENSITY_URGENT = 'urgent';

    protected $fillable = ['user_id', 'content', 'intensity_level', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public static function intensityLabels(): array
    {
        return [
            self::INTENSITY_LOW => 'Nhẹ - Gợi ý cải thiện',
            self::INTENSITY_MEDIUM => 'Vừa - Mong muốn cải thiện',
            self::INTENSITY_HIGH => 'Mạnh - Rất mong cải thiện',
            self::INTENSITY_URGENT => 'Mãnh liệt - Cần cải thiện gấp',
        ];
    }
}
