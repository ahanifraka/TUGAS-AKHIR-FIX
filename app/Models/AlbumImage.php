<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;
use Spatie\Translatable\HasTranslations;

class AlbumImage extends Model
{
    use HasFactory, SoftDeletes;
    use HasTranslations;

    public $translatable = [
        'title',
        'description',
    ];

    protected $fillable = [
        'album_id',
        'image',
        'title',
        'description',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function getImageUrlAttribute()
    {
        $image = $this->image;
        if (empty($image)) {
            return asset('images/default-cover.png');
        }

        // If already an absolute URL, return as is
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        // Normalize slashes for Windows paths
        $normalizedImage = str_replace('\\', '/', $image);
        $publicPath = str_replace('\\', '/', public_path());
        $storagePublic = str_replace('\\', '/', storage_path('app/public'));

        // If absolute path inside public/, convert to relative and asset()
        if (str_starts_with($normalizedImage, $publicPath . '/')) {
            $relative = Str::after($normalizedImage, $publicPath . '/');
            return asset($relative);
        }

        // If absolute path inside storage/app/public, convert to /storage/ URL
        if (str_starts_with($normalizedImage, $storagePublic . '/')) {
            $relative = Str::after($normalizedImage, $storagePublic . '/');
            return asset('storage/' . $relative);
        }

        // Otherwise treat as relative path under public
        return asset($image);
    }

    /**
     * Convert uploaded image to WebP format
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string The path to the converted WebP image
     */
    private function convertToWebP($file, $directory = 'album-images')
    {
        try {
            $originalExtension = strtolower($file->getClientOriginalExtension());
            
            // If already WebP, just store it
            if ($originalExtension === 'webp') {
                $path = $file->store($directory, 'public');
                return 'storage/' . $path;
            }

            // Increase memory limit for large images
            $currentLimit = ini_get('memory_limit');
            if (preg_match('/^(\d+)(.)$/', $currentLimit, $matches)) {
                $currentBytes = $matches[1] * ($matches[2] == 'G' ? 1073741824 : ($matches[2] == 'M' ? 1048576 : 1024));
                if ($currentBytes < 256 * 1048576) { // Less than 256MB
                    @ini_set('memory_limit', '256M');
                }
            }

            // Create a temporary path for the original file
            $tempPath = $file->store('temp', 'public');
            $fullTempPath = storage_path('app/public/' . $tempPath);

            // Create GD image resource from the uploaded file
            $image = null;
            switch ($originalExtension) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($fullTempPath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($fullTempPath);
                    // Preserve transparency
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($fullTempPath);
                    break;
                default:
                    // Unsupported format, just store as is
                    Storage::disk('public')->delete($tempPath);
                    $path = $file->store($directory, 'public');
                    return 'storage/' . $path;
            }

            if (!$image) {
                Log::warning('Failed to create image resource for WebP conversion', [
                    'original_extension' => $originalExtension,
                    'temp_path' => $fullTempPath,
                ]);
                Storage::disk('public')->delete($tempPath);
                $path = $file->store($directory, 'public');
                return 'storage/' . $path;
            }

            // Generate unique filename for WebP
            $filename = uniqid() . '_' . time() . '.webp';
            $webpPath = $directory . '/' . $filename;
            $fullWebpPath = storage_path('app/public/' . $webpPath);

            // Ensure directory exists
            $webpDir = dirname($fullWebpPath);
            if (!file_exists($webpDir)) {
                mkdir($webpDir, 0755, true);
            }

            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);
            
            // Resize if image is too large (max 2000px on longest side)
            $maxDimension = 2000;
            if ($width > $maxDimension || $height > $maxDimension) {
                if ($width > $height) {
                    $newWidth = $maxDimension;
                    $newHeight = (int)(($height / $width) * $maxDimension);
                } else {
                    $newHeight = $maxDimension;
                    $newWidth = (int)(($width / $height) * $maxDimension);
                }
                
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                
                // Preserve transparency for PNG
                if ($originalExtension === 'png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                    imagefill($resized, 0, 0, $transparent);
                }
                
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
                
                Log::info('Resized large album image', [
                    'original' => "{$width}x{$height}",
                    'resized' => "{$newWidth}x{$newHeight}",
                ]);
            }

            // Convert to WebP with quality 85 (good balance between quality and size)
            $success = imagewebp($image, $fullWebpPath, 85);

            // Free up memory
            imagedestroy($image);
            
            // Delete temporary file
            Storage::disk('public')->delete($tempPath);
            
            // Force garbage collection
            gc_collect_cycles();

            if ($success) {
                Log::info('Album image successfully converted to WebP', [
                    'original_extension' => $originalExtension,
                    'original_size' => $file->getSize(),
                    'webp_path' => $webpPath,
                    'webp_size' => filesize($fullWebpPath),
                    'size_reduction' => round((1 - filesize($fullWebpPath) / $file->getSize()) * 100, 2) . '%',
                ]);
                return 'storage/' . $webpPath;
            } else {
                Log::warning('Failed to save WebP image, using original', [
                    'original_extension' => $originalExtension,
                ]);
                $path = $file->store($directory, 'public');
                return 'storage/' . $path;
            }
        } catch (Exception $e) {
            Log::error('Error converting album image to WebP', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Fallback: store original image
            $path = $file->store($directory, 'public');
            return 'storage/' . $path;
        }
    }

    /**
     * Handle image assignment. If an UploadedFile is provided,
     * store it on the public disk and persist the public path.
     */
    public function setImageAttribute($value)
    {
        try {
            if ($value instanceof UploadedFile) {
                // Convert to WebP and store under storage/app/public/album-images
                $this->attributes['image'] = $this->convertToWebP($value);
                return;
            }

            if (is_string($value)) {
                $trimmed = trim($value);
                $this->attributes['image'] = $trimmed !== '' ? $trimmed : null;
                return;
            }

            $this->attributes['image'] = null;
        } catch (\Throwable $e) {
            Log::error('Error in setImageAttribute', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}