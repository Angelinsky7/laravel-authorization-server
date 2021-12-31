<?php

namespace Darkink\AuthorizationServer\Providers;

use Darkink\AuthorizationServer\Policy;
use Darkink\AuthorizationServer\Services\KeyHelperService;
use Darkink\AuthorizationServer\View\Components\ButtonDot;
use Darkink\AuthorizationServer\View\Components\IconBoolTick;
use Darkink\AuthorizationServer\View\Components\ButtonRaised;
use Darkink\AuthorizationServer\View\Components\ButtonStroked;
use Darkink\AuthorizationServer\View\Components\Dropdown;
use Darkink\AuthorizationServer\View\Components\DropdownLink;
use Darkink\AuthorizationServer\View\Components\FormFieldError;
use Darkink\AuthorizationServer\View\Components\Table;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\CryptKey;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Facades\Gate;

class PolicyServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'policy');

        $this->loadViewComponentsAs('policy', [
            IconBoolTick::class,
            ButtonRaised::class,
            ButtonStroked::class,
            ButtonDot::class,
            Table::class,
            FormFieldError::class,
            Dropdown::class,
            DropdownLink::class
        ]);

        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'policy-migrations');

            $this->publishes([
                __DIR__ . '/../../resources/views' => base_path('resources/views/vendor/policy'),
            ], 'policy-views');

            $this->publishes([
                __DIR__ . '/../../config/policy.php' => config_path('policy.php'),
            ], 'policy-config');

            // $this->publishes([
            //     __DIR__.'/../public' => public_path('vendor/policy'),
            // ], 'policy-public');

            $this->commands([
                \Darkink\AuthorizationServer\Console\InstallCommand::class,
                \Darkink\AuthorizationServer\Console\KeysCommand::class,
                \Darkink\AuthorizationServer\Console\RoleCommand::class
            ]);
        }
    }

    protected function registerMigrations()
    {
        if (Policy::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/policy.php', 'policy');

        $this->registerKeyHelperService();
        $this->registerBearerTokenDecoderService();

        // $this->registerAuthorizationServer();
        // $this->registerRoleRepository();
        // $this->registerPermissionRepository();
    }

    protected function registerKeyHelperService()
    {
        $this->app->singleton(KeyHelperService::class, function ($container) {
            return new KeyHelperService(
                $this->makeCryptKey('private')
            );
        });
    }

    protected function registerBearerTokenDecoderService()
    {
        $this->app->singleton(BearerTokenDecoderService::class);
    }

    protected function registerAuthorizationServer()
    {
    }

    // protected function registerRoleRepository()
    // {
    //     $this->app->singleton(RoleRepository::class);
    // }

    protected function registerPermissionRepository()
    {
    }

    protected function makeCryptKey($type)
    {
        $key = str_replace('\\n', "\n", $this->app->make(Config::class)->get('passport.' . $type . '_key') ?? '');

        if (!$key) {
            $key = 'file://' . Policy::keyPath('oauth-' . $type . '.key');
        }

        return new CryptKey($key, null, false);
    }
}
