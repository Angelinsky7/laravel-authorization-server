<?php

namespace Darkink\AuthorizationServer;

use Darkink\AuthorizationServer\Http\Controllers\ApiRoleController;
use Darkink\AuthorizationServer\Http\Controllers\DiscoverController;
use Darkink\AuthorizationServer\Http\Controllers\RoleController;
use Darkink\AuthorizationServer\Http\Controllers\UserAuthorizationController;
use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class Policy
{

    public static $keyPath;
    public static $runsMigrations = true;
    public static $issuer = '';
    public static $roleModel = Role::class;
    public static $permissionModel = Permission::class;

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

        // Route::prefix('policy')->middleware(config('policy.route.web'))->group(function () {
        //     Route::group(['prefix' => 'role'], function () {
        //         Route::get('/', [RoleController::class, 'index'])->middleware('can:role.see')->name('policy.role.index');
        //         // Route::get('/delete-multiple', [RoleController::class, 'deleteMultiple'])->middleware('can:role.delete')->name('policy.role.delete-multiple');
        //         Route::get('/create', [RoleController::class, 'create'])->middleware('can:role.create')->name('policy.role.create');
        //         Route::post('/create', [RoleController::class, 'store'])->middleware('can:role.create')->name('policy.role.store');
        //         Route::get('/{role}', [RoleController::class, 'show'])->middleware('can:role.see')->name('policy.role.show');
        //         Route::get('/{role}/edit', [RoleController::class, 'edit'])->middleware('can:role.update')->name('policy.role.edit');
        //         Route::put('/{role}', [RoleController::class, 'update'])->middleware('can:role.update')->name('policy.role.update');
        //         Route::get('/{role}/delete', [RoleController::class, 'delete'])->middleware('can:role.delete')->name('policy.role.delete');
        //         // Route::delete('/destroy-multiple', [RoleController::class, 'destroyMultiple'])->middleware('can:role.delete')->name('policy.role.destroy-multiple');
        //         Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('can:role.delete')->name('policy.role.destroy');
        //     });

        //     Route::group(['prefix' => 'permission'], function () {
        //         Route::get('/', [RoleController::class, 'index'])->middleware('can:premission.see')->name('policy.premission.index');
        //     });
        // });

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

}
