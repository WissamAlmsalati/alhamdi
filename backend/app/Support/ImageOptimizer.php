<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores uploaded photos web-ready: scales down to a maximum side length and saves as a
 * compressed JPEG (transparent areas become white). Falls back to storing the original
 * file when GD can't read it (e.g. SVG).
 */
class ImageOptimizer
{
    public const MAX_SIDE = 1600;

    public const QUALITY = 82;

    public static function store(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $source = self::load($file->getRealPath(), strtolower($file->getClientOriginalExtension() ?: $file->extension()));
        if (! $source) {
            return $file->store($directory, $disk);
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, self::MAX_SIDE / max($width, $height));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagejpeg($canvas, null, self::QUALITY);
        $jpeg = ob_get_clean();
        imagedestroy($source);
        imagedestroy($canvas);

        $path = trim($directory, '/').'/'.Str::random(40).'.jpg';
        Storage::disk($disk)->put($path, $jpeg);

        return $path;
    }

    private static function load(string $path, string $extension)
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $image = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if ($image && in_array($extension, ['jpg', 'jpeg'], true)) {
            $image = self::applyExifOrientation($image, $path);
        }

        return $image ?: null;
    }

    // Phone photos store rotation in EXIF; apply it so the saved JPEG isn't sideways.
    private static function applyExifOrientation($image, string $path)
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }
        $exif = @exif_read_data($path);
        $angle = match ($exif['Orientation'] ?? 1) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        return $angle ? imagerotate($image, $angle, 0) : $image;
    }
}
