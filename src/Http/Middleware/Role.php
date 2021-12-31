<?php

namespace Darkink\AuthorizationServer\Http\Middleware;

use Closure;
use Illuminate\Validation\UnauthorizedException;

class Role
{
    public function handle($request, Closure $next, $roles,  $guard = null)
    {
        $authGuard = app('auth')->guard($guard);
        $roles = is_array($roles) ? $roles : explode('|', $roles);

        foreach ($roles as $role) {
            if (!$authGuard->user()->hasRole($role)) {
                throw new UnauthorizedException('User don\'t have the correct role.');
            }
        }

        return $next($request);
    }
}
