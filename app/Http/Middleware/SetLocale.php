<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['en', 'fr'];

    public function handle(Request $request, Closure $next): Response
    {
        $segment = $request->segment(1);

        if (in_array($segment, self::SUPPORTED, true)) {
            app()->setLocale($segment);
            session(['locale' => $segment]);
        } elseif ($saved = session('locale')) {
            if (in_array($saved, self::SUPPORTED, true)) {
                app()->setLocale($saved);
            }
        }

        return $next($request);
    }
}
