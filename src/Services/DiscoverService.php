<?php

namespace Darkink\AuthorizationServer\Services;

use Illuminate\Http\Request;
use League\OAuth2\Server\CryptKey;

class DiscoverService
{
    // public static $publicKeyTypes = [
    //     0 => 'rsa',
    //     1 => 'dsa',
    //     3 => 'dh',
    //     4 => 'ec'
    // ];

    public static $algs = [
        0 => 'RS256',
    ];

    public string $host;
    public string $alg;
    public int $tokenExpiration;
    // public string $publicKeyType;

    protected KeyHelperService $keyHelperService;

    public function __construct(KeyHelperService $keyHelperService)
    {
        $request = Request::capture();
        $this->host = config('passport.token_iss', $request->getSchemeAndHttpHost());
        $this->alg = config('passport.token_alg', self::$algs[0]);
        $this->tokenExpiration = config('passport.token_expiration', 900);
        // $this->alg = config('passport.publicKeyType', self::$publicKeyType[0]);
        $this->keyHelperService = $keyHelperService;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return CryptKey
     */
    public function getPrivateKey()
    {
        return $this->keyHelperService->getPrivateKey();
    }
}
