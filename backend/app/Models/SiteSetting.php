<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    /** Site images editable from the admin panel. When empty, the site shows its built-in drawings. */
    public const IMAGES = [
        'about_image' => 'الصورة الكبيرة في قسم «من نحن»',
        'quick_pistachio' => 'الفستق',
        'quick_almond' => 'اللوز',
        'quick_cashew' => 'الكاجو',
        'quick_hazelnut' => 'البندق',
        'quick_coffee' => 'بن الحامي',
        'quick_blend' => 'خلطات ميكس',
    ];

    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** @return array<string, string|null> key => stored path */
    public static function images(): array
    {
        $stored = static::whereIn('key', array_keys(self::IMAGES))->pluck('value', 'key');

        return collect(self::IMAGES)->mapWithKeys(fn ($label, $key) => [$key => $stored[$key] ?? null])->all();
    }
}
