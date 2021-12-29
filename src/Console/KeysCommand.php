<?php

namespace Darkink\AuthorizationServer\Console;

use Darkink\AuthorizationServer\Policy;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use phpseclib\Crypt\RSA as LegacyRSA;
use phpseclib3\Crypt\RSA;

class KeysCommand extends Command
{
    protected $signature = 'policy:keys
                              {--force : Overwrite keys they already exist}
                              {--length=4096 : The length of the private key}';

    protected $description = 'Create the encryption keys for API authorization';

    public function handle()
    {
        [$publicKey, $privateKey] = [
            Policy::keyPath('policy-public.key'),
            Policy::keyPath('policy-private.key'),
        ];

        if ((file_exists($publicKey) || file_exists($privateKey)) && ! $this->option('force')) {
            $this->error('Encryption keys already exist. Use the --force option to overwrite them.');

            return 1;
        } else {
            if (class_exists(LegacyRSA::class)) {
                $keys = (new LegacyRSA)->createKey($this->input ? (int) $this->option('length') : 4096);

                file_put_contents($publicKey, Arr::get($keys, 'publickey'));
                file_put_contents($privateKey, Arr::get($keys, 'privatekey'));
            } else {
                $key = RSA::createKey($this->input ? (int) $this->option('length') : 4096);

                file_put_contents($publicKey, (string) $key->getPublicKey());
                file_put_contents($privateKey, (string) $key);
            }

            $this->info('Encryption keys generated successfully.');
        }

        return 0;
    }
}
