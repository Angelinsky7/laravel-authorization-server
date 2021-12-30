<?php

namespace Darkink\AuthorizationServer\Services;

use League\OAuth2\Server\CryptKey;

class KeyHelperService
{
    protected CryptKey $privateKey;

    public function __construct(CryptKey | string $privateKey)
    {
        if ($privateKey instanceof CryptKey === false) {
            $privateKey = new CryptKey($privateKey);
        }
        $this->privateKey = $privateKey;
    }

    public function getPrivateKey(): CryptKey
    {
        return $this->privateKey;
    }
}
