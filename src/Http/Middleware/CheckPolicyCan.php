<?php

namespace Darkink\AuthorizationServer\Http\Middleware;

use Closure;

class CheckPolicyCan
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
