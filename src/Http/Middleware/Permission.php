<?php

namespace Darkink\AuthorizationServer\Http\Middleware;

use Closure;
use Illuminate\Validation\UnauthorizedException;

class Permission
{
    public function handle($request, Closure $next, $permissions,  $guard = null)
    {
        $authGuard = app('auth')->guard($guard);
        $permissions = is_array($permissions) ? $permissions : explode('|', $permissions);

        foreach ($permissions as $permission) {
            if (!$authGuard->user()->hasPermission($permission)) {
                throw new UnauthorizedException('User don\'t have the correct permission.');
            }
        }

        return $next($request);
    }
}
