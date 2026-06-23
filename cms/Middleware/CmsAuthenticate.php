<?php

namespace Cms\Middleware;

use Cms\Support\CmsAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CmsAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! CmsAuth::check()) {
            if ($request->expectsJson() || $request->is('cms/api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect('/cms/login');
        }

        return $next($request);
    }
}
