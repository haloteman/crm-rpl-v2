<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'wajib-ganti-password' => \App\Http\Middleware\WajibGantiPassword::class,
        ]);

        // user yang sudah login lalu buka /login diarahkan ke beranda
        $middleware->redirectUsersTo(fn () => route('beranda'));

        // customer yang belum login saat mau memesan diarahkan ke halaman masuk customer
        $middleware->redirectGuestsTo(fn ($request) => $request->routeIs('pesanan.*') ? route('customer.login') : route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
