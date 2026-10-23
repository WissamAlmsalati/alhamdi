<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    /** platform => [label, URL pattern used for auto-detection] */
    public const PLATFORMS = [
        'facebook' => ['Facebook', '/(^|\.)(facebook\.com|fb\.com|fb\.me)/i'],
        'instagram' => ['Instagram', '/(^|\.)(instagram\.com|instagr\.am)/i'],
        'tiktok' => ['TikTok', '/(^|\.)tiktok\.com/i'],
        'x' => ['X (Twitter)', '/(^|\.)(x\.com|twitter\.com)/i'],
        'youtube' => ['YouTube', '/(^|\.)(youtube\.com|youtu\.be)/i'],
        'snapchat' => ['Snapchat', '/(^|\.)snapchat\.com/i'],
        'telegram' => ['Telegram', '/(^|\.)(t\.me|telegram\.me|telegram\.org)/i'],
        'whatsapp' => ['WhatsApp', '/(^|\.)(wa\.me|whatsapp\.com)/i'],
        'linkedin' => ['LinkedIn', '/(^|\.)linkedin\.com/i'],
        'website' => ['Website', null],
    ];

    protected $fillable = ['platform', 'url', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public static function detectPlatform(string $url): string
    {
        $host = parse_url(preg_match('~^[a-z]+://~i', $url) ? $url : 'https://'.$url, PHP_URL_HOST) ?: $url;
        foreach (self::PLATFORMS as $key => [, $pattern]) {
            if ($pattern && preg_match($pattern, $host)) {
                return $key;
            }
        }

        return 'website';
    }

    public function getLabelAttribute(): string
    {
        return self::PLATFORMS[$this->platform][0] ?? 'Website';
    }
}
