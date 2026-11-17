<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApotekerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'Silakan masuk terlebih dahulu.');
        }

        if (! Auth::user()->isApoteker()) {
            abort(403, 'Akses hanya diperbolehkan untuk Apoteker.');
        }

        return $next($request);
    }
}
