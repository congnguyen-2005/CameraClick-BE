<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Laravel 11 đã tự động có HandleCors, 
        // nhưng dòng này sẽ đảm bảo nó ưu tiên xử lý trước các middleware khác
        $middleware->prepend(\Illuminate\Http\Middleware\HandleCors::class);

        // Đăng ký các middleware tùy chỉnh của bạn
        $middleware->alias([
            'checkAdmin' => \App\Http\Middleware\CheckAdmin::class,
        ]);

        // Hỗ trợ API cho các ứng dụng SPA
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();