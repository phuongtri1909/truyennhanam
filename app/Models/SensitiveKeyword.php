<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensitiveKeyword extends Model
{
    protected $fillable = ['keyword'];

    public static function getKeywords(): array
    {
        return static::pluck('keyword')->map(fn ($k) => trim($k))->filter()->values()->toArray();
    }

    public static function maskContent(string $content): string
    {
        $keywords = static::getKeywords();
        if (empty($keywords)) {
            return $content;
        }

        $escaped = array_map('preg_quote', $keywords);
        $pattern = '/\b(' . implode('|', $escaped) . ')\b/iu';

        return preg_replace_callback($pattern, function ($m) {
            $word = $m[1];
            return '[SENSITIVE]' . $word . '[/SENSITIVE]';
        }, $content);
    }
}
