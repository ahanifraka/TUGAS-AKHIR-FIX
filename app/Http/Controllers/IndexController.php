<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\Berita;
use App\Models\BeritaCategory;
use App\Models\ContentSlider;
use App\Models\Bumd;
use App\Models\Album;
use App\Models\Regulasi;
use App\Models\ContentPage;
use App\Models\Pejabat;
use App\Models\User;
use App\Models\PermohonanInformasi;
use App\Models\PengajuanKeberatan;

class IndexController extends Controller
{
    public function index()
    {
        $slider = ContentSlider::query()
            ->latest()
            ->orderBy('created_at')
            ->where('published', 1)
            ->paginate(3)
            ->withQueryString();

        // Ensure slider items expose localized title strings and usable image URLs
        $slider->getCollection()->transform(function ($s) {
            return [
                'id' => $s->id,
                'title' => $s->title, // Spatie returns current-locale string
                'description' => $s->description, // localized string
                'image' => $s->image_url ?? $s->image,
                'link' => $s->link,
                'published' => (bool) $s->published,
                'created_at' => $s->created_at?->toDateTimeString(),
                'updated_at' => $s->updated_at?->toDateTimeString(),
            ];
        });
        $berita_populer = Berita::query()
            ->published()
            ->where('popular', 1)
            ->latest()
            ->orderBy('created_at')
            ->paginate(4)
            ->withQueryString();
        
        $beritaQuery = Berita::query()
            ->published()
            ->latest()
            ->orderBy('created_at');

        if (request()->has('category_id') && request()->category_id) {
            $beritaQuery->where('category_id', request()->category_id);
        }

        $berita = $beritaQuery->paginate(8)->withQueryString();

        // Transform berita populer to expose localized strings + full translation maps (avoid raw JSON leak)
        $berita_populer->getCollection()->transform(function ($b) {
            return [
                'id' => $b->id,
                'slug' => $b->slug,
                'title' => $b->title, // localized (current app locale)
                'title_translations' => $b->getTranslations('title'),
                'teaser' => $b->teaser,
                'teaser_translations' => $b->getTranslations('teaser'),
                'image' => $b->image_url ?? $b->image,
                'popular' => (int) $b->popular,
                'published' => (int) $b->published,
                'created_at' => $b->created_at?->toDateTimeString(),
                'updated_at' => $b->updated_at?->toDateTimeString(),
            ];
        });

        // Transform regular berita collection similarly
        $berita->getCollection()->transform(function ($b) {
            return [
                'id' => $b->id,
                'slug' => $b->slug,
                'title' => $b->title,
                'title_translations' => $b->getTranslations('title'),
                'teaser' => $b->teaser,
                'teaser_translations' => $b->getTranslations('teaser'),
                'image' => $b->image_url ?? $b->image,
                'category_id' => $b->category_id,
                'popular' => (int) $b->popular,
                'published' => (int) $b->published,
                'created_at' => $b->created_at?->toDateTimeString(),
                'updated_at' => $b->updated_at?->toDateTimeString(),
            ];
        });
        $bumd = Bumd::query()->orderBy('id', 'asc')->paginate(50)->withQueryString();
        $album = Album::query()->latest()->orderBy('created_at')->where('published', 1)->paginate(8)->withQueryString();
        // Transform album collection to include localized title + translation map (avoid JSON leak)
        $album->getCollection()->transform(function ($a) {
            return [
                'id' => $a->id,
                'title' => $a->getTranslation('title', app()->getLocale()) ?: $a->getTranslation('title', 'id') ?: (is_array($a->title) ? ($a->title['id'] ?? null) : $a->title),
                'title_translations' => $a->getTranslations('title'),
                'image' => $a->image_url ?? $a->image,
                'created_at' => $a->created_at?->toDateTimeString(),
            ];
        });

        // Group BUMDs by sektor for isometric component
        $bumdBySektor = Bumd::query()
            ->whereNotNull('sektor')
            ->where('sektor', '!=', '')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('sektor')
            ->map(function ($items) {
                return $items->map(function ($bumd) {
                    return [
                        'kode' => $bumd->kode,
                        'nama' => $bumd->nama,
                        'nama_pendek' => $bumd->nama_pendek,
                        'logo' => $bumd->logo,
                    ];
                })->values();
            });

        // Get active berita categories for filter
        $berita_categories = BeritaCategory::where('is_active', 1)
            ->orderBy('category_name')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'category_name' => $cat->category_name,
                ];
            });
        
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'slider' => $slider,
            'berita_populer' => $berita_populer,
            'berita' => $berita,
            'bumd' => $bumd,
            'album' => $album,
            'bumdBySektor' => $bumdBySektor,
            'berita_categories' => $berita_categories,
        ]);
    }

    public function dashboard()
    {
        $totalBerita = Berita::published()->count();
        $totalSliders = ContentSlider::where('published', 1)->count();
        $totalBumd = Bumd::count();
        $totalAlbum = Album::where('published', 1)->count();
        $totalRegulasi = Regulasi::where('is_active', 1)->count();
        $totalPages = ContentPage::where('published', 1)->count();
        $totalPejabat = Pejabat::where('published', 1)->count();
        $totalUsers = User::count();

        // Aggregate status counts for PPID modules
        $permohonanCounts = PermohonanInformasi::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $keberatanCounts = PengajuanKeberatan::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $permohonanStatus = [
            'pending' => $permohonanCounts['pending'] ?? 0,
            'processed' => $permohonanCounts['processed'] ?? 0,
            'completed' => $permohonanCounts['completed'] ?? 0,
            'rejected' => $permohonanCounts['rejected'] ?? 0,
        ];

        $keberatanStatus = [
            'pending' => $keberatanCounts['pending'] ?? 0,
            'processed' => $keberatanCounts['processed'] ?? 0,
            'completed' => $keberatanCounts['completed'] ?? 0,
            'rejected' => $keberatanCounts['rejected'] ?? 0,
        ];

        $beritaEvents = Berita::whereNotNull('published_at')
            ->get()
            ->map(function ($berita) {
                return [
                    'id' => $berita->id,
                    'title' => $berita->title,
                    'date' => $berita->published_at->format('Y-m-d'),
                    'published' => (bool) $berita->published,
                ];
            });

        return Inertia::render('Dashboard', [
            'totalBerita' => $totalBerita,
            'totalSliders' => $totalSliders,
            'totalBumd' => $totalBumd,
            'totalAlbum' => $totalAlbum,
            'totalRegulasi' => $totalRegulasi,
            'totalPages' => $totalPages,
            'totalPejabat' => $totalPejabat,
            'totalUsers' => $totalUsers,
            'permohonanStatus' => $permohonanStatus,
            'keberatanStatus' => $keberatanStatus,
            'beritaEvents' => $beritaEvents,
        ]);
    }
}
