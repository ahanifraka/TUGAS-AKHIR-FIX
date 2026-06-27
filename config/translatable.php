<?php

return [
    // The default locale used for translations
    'locale' => env('APP_LOCALE', config('app.locale', 'id')),

    // The fallback locale to use when a translation is missing
    'fallback_locale' => env('APP_FALLBACK_LOCALE', config('app.fallback_locale', 'id')),

    // List of supported locales for manual translations
    'locales' => ['id', 'en'],
];
