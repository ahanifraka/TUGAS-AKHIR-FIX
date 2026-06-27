<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);

        // Share localization props with the frontend (Inertia)
        Inertia::share('i18n', function () {
            $locale = app()->getLocale() ?: config('app.default_locale', 'id');
            $defaultLocale = config('app.default_locale', 'id');
            $supported = ['id', 'en'];

            $translations = $this->loadTranslations($locale);
            if (empty($translations) && $locale !== $defaultLocale) {
                $translations = $this->loadTranslations($defaultLocale);
            }

            return [
                'supportedLocales' => $supported,
                'locale' => $locale,
                'defaultLocale' => $defaultLocale,
                'translations' => $translations,
            ];
        });
    }

    /**
     * Load translations for the given locale from lang path.
     * Supports both JSON (lang/{locale}.json) and PHP files (lang/{locale}/*.php).
     * Returns a flat associative array suitable for the frontend.
     */
    protected function loadTranslations(string $locale): array
    {
        $langPath = lang_path();
        $all = [];

        // Merge JSON translations if available
        $jsonFile = $langPath . DIRECTORY_SEPARATOR . $locale . '.json';
        if (is_file($jsonFile)) {
            $json = @file_get_contents($jsonFile);
            $data = $json ? json_decode($json, true) : [];
            if (is_array($data)) {
                $all = $this->arrayMergeRecursiveDistinct($all, $data);
            }
        }

        // Merge PHP array files under lang/{locale} recursively; map folder path to nested keys
        $dir = $langPath . DIRECTORY_SEPARATOR . $locale;
        if (is_dir($dir)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                    $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($dir) + 1));
                    $withoutExt = preg_replace('/\.php$/i', '', $relative);
                    $segments = array_values(array_filter(explode('/', $withoutExt)));
                    $arr = @include $file->getPathname();
                    if (is_array($arr) && !empty($segments)) {
                        $this->arraySetNested($all, $segments, $arr);
                    }
                }
            }
        }

        return $all;
    }

    /**
     * Recursively set nested array value by path segments, merging when needed.
     */
    protected function arraySetNested(array &$target, array $segments, array $value): void
    {
        $ref =& $target;
        $last = array_pop($segments);
        foreach ($segments as $seg) {
            if (!isset($ref[$seg]) || !is_array($ref[$seg])) {
                $ref[$seg] = [];
            }
            $ref =& $ref[$seg];
        }
        if (isset($ref[$last]) && is_array($ref[$last])) {
            $ref[$last] = $this->arrayMergeRecursiveDistinct($ref[$last], $value);
        } else {
            $ref[$last] = $value;
        }
    }

    /**
     * Merge arrays recursively with second array overriding scalar values of the first.
     */
    protected function arrayMergeRecursiveDistinct(array $a, array $b): array
    {
        foreach ($b as $key => $val) {
            if (is_array($val) && isset($a[$key]) && is_array($a[$key])) {
                $a[$key] = $this->arrayMergeRecursiveDistinct($a[$key], $val);
            } else {
                $a[$key] = $val;
            }
        }
        return $a;
    }
}
