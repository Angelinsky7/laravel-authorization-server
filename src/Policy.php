<?php

namespace Darkink\AuthorizationServer;

use App\Models\User;
use App\Models\Client as OAuthClient;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use Darkink\AuthorizationServer\Helpers\FlashMessage;
use Darkink\AuthorizationServer\Helpers\FlashMessageSize;
use Darkink\AuthorizationServer\Http\Controllers\ApiRoleController;
use Darkink\AuthorizationServer\Http\Controllers\DiscoverController;
use Darkink\AuthorizationServer\Http\Controllers\EvaluatorController;
use Darkink\AuthorizationServer\Http\Controllers\RoleController;
use Darkink\AuthorizationServer\Http\Controllers\UserAuthorizationController;
use Darkink\AuthorizationServer\Models\AggregatedPolicy;
use Darkink\AuthorizationServer\Models\Client;
use Darkink\AuthorizationServer\Models\ClientPolicy;
use Darkink\AuthorizationServer\Models\Group;
use Darkink\AuthorizationServer\Models\GroupPolicy;
use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Policy as ModelsPolicy;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\ResourcePermission;
use Darkink\AuthorizationServer\Models\Role;
use Darkink\AuthorizationServer\Models\RolePolicy;
use Darkink\AuthorizationServer\Models\Scope;
use Darkink\AuthorizationServer\Models\ScopePermission;
use Darkink\AuthorizationServer\Models\TimePolicy;
use Darkink\AuthorizationServer\Models\UserPolicy;
use Darkink\AuthorizationServer\Services\_Default\DefaultCache;
use Darkink\AuthorizationServer\Services\_Default\DefaultEvaluatorService;
use Darkink\AuthorizationServer\Services\Caching\CachingEvaluatorService;
use Darkink\AuthorizationServer\Services\ICache;
use Darkink\AuthorizationServer\Services\IEvaluatorService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class Policy
{

    public static $keyPath;
    public static $runsMigrations = true;
    public static $issuer = '';

    #region model

    public static $scopeModel = Scope::class;
    public static $resourceModel = Resource::class;
    public static $roleModel = Role::class;
    public static $permissionModel = Permission::class;
    public static $scopePermissionModel = ScopePermission::class;
    public static $resourcePermissionModel = ResourcePermission::class;
    public static $groupModel = Group::class;
    public static $userModel = User::class;
    public static $policyModel = ModelsPolicy::class;
    public static $groupPolicyModel = GroupPolicy::class;
    public static $rolePolicyModel = RolePolicy::class;
    public static $userPolicyModel = UserPolicy::class;
    public static $clientPolicyModel = ClientPolicy::class;
    public static $timePolicyModel = TimePolicy::class;
    public static $aggregatedPolicyModel = AggregatedPolicy::class;
    public static $clientModel = Client::class;
    public static $oauthClientModel = OAuthClient::class;

    #endregion

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
            // Route::get('/authorization', [UserAuthorizationController::class, 'index'])->name('api.policy.authorization.index');
            Route::get('/authorization', [EvaluatorController::class, 'process'])->name('api.policy.authorization.index');
        });

        // Route::prefix('api')->middleware(config('policy.route.api'))->group(function () {
        //     Route::prefix('policy')->group(function () {
        //         Route::group(['prefix' => 'role'], function () {
        //             Route::get('/', [ApiRoleController::class, 'index'])->name('api.policy.role.index');
        //         });

        //         Route::group(['prefix' => 'permission'], function () {
        //             Route::get('/', [RoleController::class, 'index'])->name('api.policy.permission.index');
        //         });
        //     });
        // });
    }

    public static function registerExceptionHanlder(ExceptionHandler $hanlder)
    {
        $command = request()->server('argv', []);
        if (!in_array('artisan', $command)) {
            $hanlder->reportable(function (QueryException $e) {
                request()->session()->flash('error_message', new FlashMessage($e->errorInfo[2], false, 3000, FlashMessageSize::NORMAL));
                $error = ValidationException::withMessages([]);
                throw $error;
            });
        }
    }

    public static function registerDefaultServices()
    {
        self::registerDefaultEvaluatorService();
        // $this->registerAuthorizationServer();
        // $this->registerRoleRepository();
        // $this->registerPermissionRepository();
    }

    public static function registerCachingServices()
    {
        self::registerCachingEvaluatorService();
        // $this->registerAuthorizationServer();
        // $this->registerRoleRepository();
        // $this->registerPermissionRepository();
    }

    protected static function registerDefaultEvaluatorService()
    {
        App::bind(IEvaluatorService::class, DefaultEvaluatorService::class);
    }

    protected static function registerCachingEvaluatorService()
    {
        App::when(CachingEvaluatorService::class)
            ->needs(IEvaluatorService::class)
            ->give(DefaultEvaluatorService::class);
        App::when(CachingEvaluatorService::class)
            ->needs(ICache::class)
            ->give(fn () => new DefaultCache(EvaluatorRequest::class));
        App::bind(IEvaluatorService::class, CachingEvaluatorService::class);
    }

    protected static function registerAuthorizationServer()
    {
    }

    // protected function registerRoleRepository()
    // {
    //     $this->app->singleton(RoleRepository::class);
    // }

    protected static function registerPermissionRepository()
    {
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

    #region scope

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

    #endregion

    #region resource

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

    #endregion

    #region role

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

    #endregion

    #region permission

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

    #endregion

    #region scopePermission

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

    #endregion

    #region resourcePermission

    public static function useResourcePermissionModel($resourcePermissionModel)
    {
        static::$resourcePermissionModel = $resourcePermissionModel;
    }

    public static function resourcePermissionModel()
    {
        return static::$resourcePermissionModel;
    }

    public static function resourcePermission(): ResourcePermission
    {
        return new static::$resourcePermissionModel;
    }

    #endregion

    #region group

    public static function useGroupModel($groupModel)
    {
        static::$groupModel = $groupModel;
    }

    public static function groupModel()
    {
        return static::$groupModel;
    }

    public static function group(): Group
    {
        return new static::$groupModel;
    }

    #endregion

    #region user

    public static function useUserModel($userModel)
    {
        static::$userModel = $userModel;
    }

    public static function userModel()
    {
        return static::$userModel;
    }

    public static function user(): User
    {
        return new static::$userModel;
    }

    #endregion

    #region policy

    public static function usePolicyModel($policyModel)
    {
        static::$policyModel = $policyModel;
    }

    public static function policyModel()
    {
        return static::$policyModel;
    }

    public static function policy(): ModelsPolicy
    {
        return new static::$policyModel;
    }

    #endregion

    #region groupPolicy

    public static function useGroupPolicyModel($groupPolicyModel)
    {
        static::$groupPolicyModel = $groupPolicyModel;
    }

    public static function groupPolicyModel()
    {
        return static::$groupPolicyModel;
    }

    public static function groupPolicy(): GroupPolicy
    {
        return new static::$groupPolicyModel;
    }

    #endregion

    #region rolePolicy

    public static function useRolePolicyModel($rolePolicyModel)
    {
        static::$rolePolicyModel = $rolePolicyModel;
    }

    public static function rolePolicyModel()
    {
        return static::$rolePolicyModel;
    }

    public static function rolePolicy(): RolePolicy
    {
        return new static::$rolePolicyModel;
    }

    #endregion

    #region userPolicy

    public static function useUserPolicyModel($userPolicyModel)
    {
        static::$userPolicyModel = $userPolicyModel;
    }

    public static function userPolicyModel()
    {
        return static::$userPolicyModel;
    }

    public static function userPolicy(): UserPolicy
    {
        return new static::$userPolicyModel;
    }

    #endregion

    #region clientPolicy

    public static function useClientPolicyModel($clientPolicyModel)
    {
        static::$clientPolicyModel = $clientPolicyModel;
    }

    public static function clientPolicyModel()
    {
        return static::$clientPolicyModel;
    }

    public static function clientPolicy(): ClientPolicy
    {
        return new static::$clientPolicyModel;
    }

    #endregion

    #region timePolicy

    public static function useTimePolicyModel($timePolicyModel)
    {
        static::$timePolicyModel = $timePolicyModel;
    }

    public static function timePolicyModel()
    {
        return static::$timePolicyModel;
    }

    public static function timePolicy(): TimePolicy
    {
        return new static::$timePolicyModel;
    }

    #endregion

    #region aggregatedPolicy

    public static function useAggregatedPolicyModel($aggregatedPolicyModel)
    {
        static::$aggregatedPolicyModel = $aggregatedPolicyModel;
    }

    public static function aggregatedPolicyModel()
    {
        return static::$aggregatedPolicyModel;
    }

    public static function aggregatedPolicy(): AggregatedPolicy
    {
        return new static::$aggregatedPolicyModel;
    }

    #endregion

    #region client

    public static function useClientModel($clientModel)
    {
        static::$clientModel = $clientModel;
    }

    public static function clientModel()
    {
        return static::$clientModel;
    }

    public static function client(): Client
    {
        return new static::$clientModel;
    }

    #endregion

    #region oauthClient

    public static function useOauthClientModel($oauthClientModel)
    {
        static::$oauthClientModel = $oauthClientModel;
    }

    public static function oauthClientModel()
    {
        return static::$oauthClientModel;
    }

    public static function oauthClient(): oauthClient
    {
        return new static::$oauthClientModel;
    }

    #endregion

}
