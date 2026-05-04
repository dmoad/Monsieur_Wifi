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
        $query = (string) $request->query('locale', '');

        if (in_array($segment, self::SUPPORTED, true)) {
            app()->setLocale($segment);
            session(['locale' => $segment]);
        } elseif (in_array($query, self::SUPPORTED, true)) {
            app()->setLocale($query);
            session(['locale' => $query]);
        } elseif ($saved = session('locale')) {
            if (in_array($saved, self::SUPPORTED, true)) {
                app()->setLocale($saved);
            }
        }

        return $next($request);
    }
}
