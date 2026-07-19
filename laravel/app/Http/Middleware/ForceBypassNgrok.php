<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceBypassNgrok
{
    public function handle(Request $request, Closure $next): Response
    {
        // 🟢 Tembus paksa halaman interupsi/warning bawaan Ngrok gratisan
        $request->headers->set('ngrok-skip-browser-warning', 'true');

        $response = $next($request);

        // 🟢 Daftarkan header CORS agar browser Ionic diizinkan menarik data
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, ngrok-skip-browser-warning');

        return $response;
    }
}