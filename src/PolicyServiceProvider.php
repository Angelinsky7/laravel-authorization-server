<?php

namespace Darkink\AuthorizationServer;

use Darkink\AuthorizationServer\Policy;
use Darkink\AuthorizationServer\Services\_Default\DefaultCache;
use Darkink\AuthorizationServer\Services\_Default\DefaultEvaluatorService;
use Darkink\AuthorizationServer\Services\ICache;
use Darkink\AuthorizationServer\Services\IEvaluatorService;
use Darkink\AuthorizationServer\Services\KeyHelperService;
use Darkink\AuthorizationServer\View\Components\ButtonCancel;
use Darkink\AuthorizationServer\View\Components\ButtonDot;
use Darkink\AuthorizationServer\View\Components\IconBoolTick;
use Darkink\AuthorizationServer\View\Components\ButtonRaised;
use Darkink\AuthorizationServer\View\Components\ButtonStroked;
use Darkink\AuthorizationServer\View\Components\ButtonSubmit;
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
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'policy-migrations');

            // $this->publishes([
            //     __DIR__ . '/../resources/views' => base_path('resources/views/vendor/policy'),
            // ], 'policy-views');

            $this->publishes([
                __DIR__ . '/../config/policy.php' => config_path('policy.php'),
            ], 'policy-config');

            // $this->publishes([
            //     __DIR__.'/../public/css/app.css' => base_path('resources/css/vendor/laravel-authorization-server.css'),
            // ], 'policy-public-css');

            // $this->publishes([
            //     __DIR__.'/../public/js/app.js' => base_path('resources/js/vendor/laravel-authorization-server.js'),
            // ], 'policy-public-js');

            $this->commands([
                \Darkink\AuthorizationServer\Console\InstallCommand::class,
                \Darkink\AuthorizationServer\Console\KeysCommand::class,
                \Darkink\AuthorizationServer\Console\RoleCommand::class
            ]);
        }

        $this->registerHelpers();
    }

    protected function registerMigrations()
    {
        if (Policy::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    public function register(){
        $this->mergeConfigFrom(__DIR__ . '/../config/policy.php', 'policy');

        $this->registerKeyHelperService();
        $this->registerBearerTokenDecoderService();
        $this->registerCacheService();
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

    protected function registerCacheService()
    {
        $this->app->bind(ICache::class, DefaultCache::class);
    }

    protected function registerHelpers()
    {
        if (file_exists($file = __DIR__ . '/policyHelper.php')) {
            require_once $file;
        }
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
