<?php

namespace Darkink\AuthorizationServer;

use Darkink\AuthorizationServer\Http\Controllers\ApiRoleController;
use Darkink\AuthorizationServer\Http\Controllers\DiscoverController;
use Darkink\AuthorizationServer\Http\Controllers\RoleController;
use Darkink\AuthorizationServer\Http\Controllers\UserAuthorizationController;
use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\Role;
use Darkink\AuthorizationServer\Models\Scope;
use Darkink\AuthorizationServer\Models\ScopePermission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class Policy
{

    public static $keyPath;
    public static $runsMigrations = true;
    public static $issuer = '';

    public static $scopeModel = Scope::class;
    public static $resourceModel = Resource::class;
    public static $roleModel = Role::class;
    public static $permissionModel = Permission::class;
    public static $scopePermissionModel = ScopePermission::class;

    public static function issuer(string $issuer)
    {
        static::$issuer = $issuer;
    }

    public static function loadKeysFrom($path)
    {
        static::$keyPath = $path;
    }

    public static function keyPath($file)
    {
        $file = ltrim($file, '/\\');

        return static::$keyPath
            ? rtrim(static::$keyPath, '/\\') . DIRECTORY_SEPARATOR . $file
            : storage_path($file);
    }

    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static;
    }

    public static function routes()
    {
        // $defaultOptions = [
        //     'prefix' => 'dummy',
        //     'namespace' => '\Darkink\AuthorizationServer\Http\Controllers',
        // ];

        // $options = array_merge($defaultOptions, $options);

        // Route::group($options, function ($router) use ($callback) {
        //     $callback(new RouteRegistrar($router));
        // });

        Route::prefix('.well-known')->group(function () {
            Route::prefix('/policy-configuration')->group(function () {
                Route::get('', [DiscoverController::class, 'index'])->name('policy.discovery.index');
                Route::get('/jwks', [DiscoverController::class, 'jwks'])->name('policy.discovery.jwks');
            });
        });

        Route::prefix('policy')->middleware(config('policy.route.api'))->group(function () {
            Route::get('/authorization', [UserAuthorizationController::class, 'index'])->name('api.policy.authorization.index');
        });

        Route::prefix('api')->middleware(config('policy.route.api'))->group(function () {
            Route::prefix('policy')->group(function () {
                Route::group(['prefix' => 'role'], function () {
                    Route::get('/', [ApiRoleController::class, 'index'])->name('api.policy.role.index');
                });

                Route::group(['prefix' => 'permission'], function () {
                    Route::get('/', [RoleController::class, 'index'])->name('api.policy.permission.index');
                });
            });
        });
    }

    public static function gates()
    {
        // Gate::after(function ($user, $ability, $result, $arguments) {
        //     if ($user->hasRole('admin')) {
        //         return true;
        //     }
        //     return $user->hasPermission($ability);
        // });

        Gate::after(function ($user, $ability, $result, $arguments) {
            return true;
        });
    }

    public static function useScopeModel($scopeModel)
    {
        static::$scopeModel = $scopeModel;
    }

    public static function ScopeModel()
    {
        return static::$scopeModel;
    }

    public static function scope(): Scope
    {
        return new static::$scopeModel;
    }

    public static function useResourceModel($resourceModel)
    {
        static::$resourceModel = $resourceModel;
    }

    public static function resourceModel()
    {
        return static::$resourceModel;
    }

    public static function resource(): Resource
    {
        return new static::$resourceModel;
    }

    public static function useRoleModel($roleModel)
    {
        static::$roleModel = $roleModel;
    }

    public static function roleModel()
    {
        return static::$roleModel;
    }

    public static function role(): Role
    {
        return new static::$roleModel;
    }

    public static function usePermissionModel($permissionModel)
    {
        static::$permissionModel = $permissionModel;
    }

    public static function permissionModel()
    {
        return static::$permissionModel;
    }

    public static function permission(): Permission
    {
        return new static::$permissionModel;
    }

    public static function useScopePermissionModel($scopePermissionModel)
    {
        static::$scopePermissionModel = $scopePermissionModel;
    }

    public static function scopePermissionModel()
    {
        return static::$scopePermissionModel;
    }

    public static function scopePermission(): ScopePermission
    {
        return new static::$scopePermissionModel;
    }

}
