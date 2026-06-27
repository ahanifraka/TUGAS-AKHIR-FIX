<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\PermohonanInformasi;
use App\Models\PengajuanKeberatan;
use Umami\UmamiFacade as Umami;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [],
                ] : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'kode_unik' => $request->session()->get('kode_unik'),
                'pin' => $request->session()->get('pin'),
            ],
            'i18n' => function () {
                $locale = app()->getLocale();
                $path = base_path("lang/{$locale}.json");
                return [
                    'locale' => $locale,
                    'defaultLocale' => config('app.fallback_locale', 'id'),
                    'supportedLocales' => ['id', 'en'],
                    'translations' => file_exists($path) ? json_decode(file_get_contents($path), true) : [],
                ];
            },
            'ppid_notifications' => function () use ($user) {
                if (!$user) {
                    return null;
                }
                
                // Check if user has editor or admin role
                $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];
                if (!in_array('editor', $roles) && !in_array('admin', $roles)) {
                    return null;
                }
                
                return [
                    'permohonan_informasi' => PermohonanInformasi::where('status', 'pending')->count(),
                    'pengajuan_keberatan' => PengajuanKeberatan::where('status', 'pending')->count(),
                    'total' => PermohonanInformasi::where('status', 'pending')->count() + 
                              PengajuanKeberatan::where('status', 'pending')->count(),
                    'recent' => [
                        'permohonan' => PermohonanInformasi::where('status', 'pending')
                            ->latest()
                            ->take(5)
                            ->get(['id', 'kode_unik', 'nama', 'created_at']),
                        'keberatan' => PengajuanKeberatan::where('status', 'pending')
                            ->latest()
                            ->take(5)
                            ->get(['id', 'kode_unik', 'nama', 'created_at']),
                    ]
                ];
            },
            'umami' => function () {
                try {
                    // Check required config to avoid abort(421) in library
                    if (!config('umami.url') || !config('umami.username') || !config('umami.password') || !config('umami.website_id')) {
                        return null;
                    }

                    $siteId = config('umami.website_id');
                    $originalCacheKey = config('umami.cache_key');

                    $options = [
                        'start_at' => now()->startOfYear(),
                        'end_at' => now(),
                    ];

                    config(['umami.cache_key' => 'umami_total']);
                    $stats = Umami::query($siteId, 'stats', $options);

                    if (!is_array($stats)) {
                        config(['umami.cache_key' => $originalCacheKey]);
                        return null;
                    }

                    // Get today's stats
                    $todayOptions = [
                        'start_at' => now()->startOfDay(),
                        'end_at' => now(),
                    ];

                    config(['umami.cache_key' => 'umami_today']);
                    $todayStats = Umami::query($siteId, 'stats', $todayOptions);

                    config(['umami.cache_key' => $originalCacheKey]);

                    return [
                        'visitors' => $stats['visitors'] ?? 0,
                        'pageviews' => $stats['pageviews'] ?? 0,
                        'visitors_today' => is_array($todayStats) ? ($todayStats['visitors'] ?? 0) : 0,
                    ];
                } catch (\Throwable $e) {
                    return null;
                }
            }
        ];
    }
}
