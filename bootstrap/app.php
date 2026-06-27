<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            // Ensure locale is set early for every web request
            SetLocale::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Register middleware aliases (e.g., for Spatie Permission)
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        $middleware->trustProxies(at: [
            '*',
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        
        Integration::handles($exceptions);
        
        // 403: Access denied (AuthorizationException)
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Akses ditolak'
                ], 403);
            }

            return \Inertia\Inertia::render('Error403', [
                'status' => 403,
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini.'
            ])->toResponse($request)->setStatusCode(403);
        });

        // 403: Access denied (Spatie Permission Unauthorized)
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Akses ditolak'
                ], 403);
            }

            return \Inertia\Inertia::render('Error403', [
                'status' => 403,
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini.'
            ])->toResponse($request)->setStatusCode(403);
        });

        // 403: Access denied (Symfony AccessDenied)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Akses ditolak'
                ], 403);
            }

            return \Inertia\Inertia::render('Error403', [
                'status' => 403,
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini.'
            ])->toResponse($request)->setStatusCode(403);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Halaman tidak ditemukan'
                ], 404);
            }

            return \Inertia\Inertia::render('Error404', [
                'status' => 404,
                'message' => 'Halaman yang Anda cari tidak dapat ditemukan.'
            ])->toResponse($request)->setStatusCode(404);
        });
    })->create();
