<?php

namespace Darkink\AuthorizationServer\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'policy:install
                            {--force : Overwrite keys they already exist}
                            {--length=4096 : The length of the private key}';

    protected $description = 'Run the commands necessary to prepare Authorization Server for use';

    public function handle()
    {
        // $provider = in_array('users', array_keys(config('auth.providers'))) ? 'users' : null;

        $this->call('policy:keys', ['--force' => $this->option('force'), '--length' => $this->option('length')]);
    }
}
