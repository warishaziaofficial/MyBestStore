<?php

namespace Cms\Middleware;

use Cms\Support\CmsAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CmsRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! in_array(CmsAuth::role(), $roles, true)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
