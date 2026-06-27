<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Berita;
use App\Models\ContentSlider;
use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Collection;

class MediaController extends Controller
{
    /**
     * Display a listing of media files for authenticated users.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $source = $request->query('source'); // Filter by source: berita, slider, album, files

        // Handle per-page pagination with whitelist
        $perPage = (int) $request->query('per_page', 10);
        $allowedPerPage = [5, 10, 20, 50];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        // Collect all media from different sources
        $allMedia = collect();

        // Add files from File model (original functionality)
        if (!$source || $source === 'files') {
            $filesQuery = File::query();
            if ($search) {
                $filesQuery->where(function ($q) use ($search) {
                    $q->where('filename', 'like', '%' . $search . '%')
                      ->orWhere('mime_type', 'like', '%' . $search . '%')
                      ->orWhere('path', 'like', '%' . $search . '%');
                });
            }
            
            $files = $filesQuery->latest('updated_at')->get();
            foreach ($files as $file) {
                $allMedia->push([
                    'id' => 'file_' . $file->id,
                    'original_id' => $file->id,
                    'filename' => $file->filename,
                    'title' => $file->filename,
                    'mime_type' => $file->mime_type,
                    'size' => (int) $file->size,
                    'path' => $file->path,
                    'url' => $file->path ? asset($file->path) : null,
                    'source' => 'files',
                    'source_label' => 'File Upload',
                    'updated_at' => $file->updated_at,
                    'created_at' => $file->created_at,
                ]);
            }
        }

        // Add images from Berita
        if (!$source || $source === 'berita') {
            $beritaQuery = Berita::whereNotNull('image')->where('image', '!=', '');
            if ($search) {
                $beritaQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('image', 'like', '%' . $search . '%');
                });
            }
            
            $beritas = $beritaQuery->latest('updated_at')->get();
            foreach ($beritas as $berita) {
                $filename = basename($berita->image);
                $allMedia->push([
                    'id' => 'berita_' . $berita->id,
                    'original_id' => $berita->id,
                    'filename' => $filename,
                    'title' => $berita->title,
                    'mime_type' => $this->getMimeTypeFromPath($berita->image),
                    'size' => null,
                    'path' => $berita->image,
                    'url' => $berita->image_url,
                    'source' => 'berita',
                    'source_label' => 'Berita',
                    'updated_at' => $berita->updated_at,
                    'created_at' => $berita->created_at,
                ]);
            }
        }

        // Add images from ContentSlider
        if (!$source || $source === 'slider') {
            $sliderQuery = ContentSlider::whereNotNull('image')->where('image', '!=', '');
            if ($search) {
                $sliderQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('image', 'like', '%' . $search . '%');
                });
            }
            
            $sliders = $sliderQuery->latest('updated_at')->get();
            foreach ($sliders as $slider) {
                $filename = basename($slider->image);
                $allMedia->push([
                    'id' => 'slider_' . $slider->id,
                    'original_id' => $slider->id,
                    'filename' => $filename,
                    'title' => $slider->title,
                    'mime_type' => $this->getMimeTypeFromPath($slider->image),
                    'size' => null,
                    'path' => $slider->image,
                    'url' => $slider->image_url,
                    'source' => 'slider',
                    'source_label' => 'Content Slider',
                    'updated_at' => $slider->updated_at,
                    'created_at' => $slider->created_at,
                ]);
            }
        }

        // Add images from Album (cover images)
        if (!$source || $source === 'album') {
            $albumQuery = Album::whereNotNull('image')->where('image', '!=', '');
            if ($search) {
                $albumQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('image', 'like', '%' . $search . '%');
                });
            }
            
            $albums = $albumQuery->latest('updated_at')->get();
            foreach ($albums as $album) {
                $filename = basename($album->image);
                $allMedia->push([
                    'id' => 'album_' . $album->id,
                    'original_id' => $album->id,
                    'filename' => $filename,
                    'title' => $album->title . ' (Cover)',
                    'mime_type' => $this->getMimeTypeFromPath($album->image),
                    'size' => null,
                    'path' => $album->image,
                    'url' => asset($album->image),
                    'source' => 'album',
                    'source_label' => 'Album Cover',
                    'updated_at' => $album->updated_at,
                    'created_at' => $album->created_at,
                ]);
            }
        }

        // Add images from AlbumImage
        if (!$source || $source === 'album_image') {
            $albumImageQuery = AlbumImage::with('album')->whereNotNull('image')->where('image', '!=', '');
            if ($search) {
                $albumImageQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('image', 'like', '%' . $search . '%')
                      ->orWhereHas('album', function ($albumQ) use ($search) {
                          $albumQ->where('title', 'like', '%' . $search . '%');
                      });
                });
            }
            
            $albumImages = $albumImageQuery->latest('updated_at')->get();
            foreach ($albumImages as $albumImage) {
                $filename = basename($albumImage->image);
                $title = $albumImage->title ?: ($albumImage->album ? $albumImage->album->title . ' - Image' : 'Album Image');
                $allMedia->push([
                    'id' => 'album_image_' . $albumImage->id,
                    'original_id' => $albumImage->id,
                    'album_id' => $albumImage->album_id,
                    'filename' => $filename,
                    'title' => $title,
                    'mime_type' => $this->getMimeTypeFromPath($albumImage->image),
                    'size' => null,
                    'path' => $albumImage->image,
                    'url' => asset($albumImage->image),
                    'source' => 'album_image',
                    'source_label' => 'Album Image',
                    'updated_at' => $albumImage->updated_at,
                    'created_at' => $albumImage->created_at,
                ]);
            }
        }

        // Sort by updated_at desc
        $allMedia = $allMedia->sortByDesc('updated_at');

        // Manual pagination
        $total = $allMedia->count();
        $currentPage = (int) $request->query('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedMedia = $allMedia->slice($offset, $perPage)->values();

        // Calculate pagination meta
        $lastPage = (int) ceil($total / $perPage);
        $hasMorePages = $currentPage < $lastPage;
        $hasPages = $lastPage > 1;

        // Build pagination URLs
        $baseUrl = $request->url();
        $queryParams = $request->query();
        
        $buildUrl = function ($page) use ($baseUrl, $queryParams) {
            $params = array_merge($queryParams, ['page' => $page]);
            return $baseUrl . '?' . http_build_query($params);
        };

        $data = [
            'data' => $paginatedMedia->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'original_id' => $item['original_id'],
                    'filename' => $item['filename'],
                    'title' => $item['title'],
                    'mime_type' => $item['mime_type'],
                    'size' => $item['size'],
                    'path' => $item['path'],
                    'url' => $item['url'],
                    'source' => $item['source'],
                    'source_label' => $item['source_label'],
                    'updated_at' => $item['updated_at']?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $buildUrl(1),
                'last' => $buildUrl($lastPage),
                'prev' => $currentPage > 1 ? $buildUrl($currentPage - 1) : null,
                'next' => $hasMorePages ? $buildUrl($currentPage + 1) : null,
            ],
            'meta' => [
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
            ],
        ];

        return Inertia::render('Media/Index', [
            'files' => $data,
            'filters' => [
                'search' => $search,
                'source' => $source,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Get MIME type from file path/extension
     */
    private function getMimeTypeFromPath($path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'bmp' => 'image/bmp',
        ];

        return $mimeTypes[$extension] ?? 'image/unknown';
    }
}