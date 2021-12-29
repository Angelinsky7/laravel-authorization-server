<?php

namespace Darkink\AuthorizationServer\Providers;

use Darkink\AuthorizationServer\Policy;
use Error;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\CryptKey;

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
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'policy-migrations');

            // $this->publishes([
            //     __DIR__.'/../resources/views' => base_path('resources/views/vendor/policy'),
            // ], 'policy-views');

            $this->publishes([
                __DIR__ . '/../../config/policy.php' => config_path('policy.php'),
            ], 'policy-config');

            $this->commands([
                \Darkink\AuthorizationServer\Console\InstallCommand::class,
                \Darkink\AuthorizationServer\Console\KeysCommand::class
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

        $this->registerAuthorizationServer();
        $this->registerRoleRepository();
        $this->registerPermissionRepository();
    }

    protected function registerAuthorizationServer()
    {
    }

    protected function registerRoleRepository()
    {
    }

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
