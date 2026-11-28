<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\CheckUserBanned::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'admin.panel' => \App\Http\Middleware\EnsurePanelAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn ($request) => $request->expectsJson());

        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            $status = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                ? $e->getStatusCode()
                : ($e instanceof \Illuminate\Auth\Access\AuthorizationException ? 403 : null);

            // Permission-denied inside the admin panel: bounce back with a
            // flash popup instead of a hard error page. Visitors without ANY
            // panel access are sent to the storefront; panel users are sent
            // back to the page they came from (fallback: dashboard).
            if ($status === 403 && $request->user() && $request->is('admin*')) {
                if (!\App\Http\Middleware\EnsurePanelAccess::allows($request->user())) {
                    return redirect()->to('/')->with('error', 'You are not authorized.');
                }

                return redirect()
                    ->back(302, [], route('admin.dashboard'))
                    ->with('error', 'You are not authorized to access that page.');
            }

            if (!$request->expectsJson() && $status !== null) {
                return \Inertia\Inertia::render('Error', ['status' => $status])
                    ->toResponse($request)
                    ->setStatusCode($status);
            }

            return null;
        });

        $exceptions->respond(function (\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response $response, \Throwable $e, \Illuminate\Http\Request $request) {
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                if (str_starts_with($request->path(), 'admin')) {
                    return redirect()->guest(route('admin.login'));
                }
            }
            return $response;
        });
    })->create();
