<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra role: 1 là admin, hoặc so sánh chuỗi 'admin' tùy DB của bạn
        if ($request->user() && ($request->user()->roles === 'admin' || $request->user()->roles === 1)) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'message' => 'Bạn không có quyền Admin để thực hiện hành động này!'
        ], 403);
    }
}