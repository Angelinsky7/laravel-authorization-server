<?php

namespace Darkink\AuthorizationServer\Http\Controllers;

use Darkink\AuthorizationServer\Http\Requests\AuthorizationRequest;
use Darkink\AuthorizationServer\Http\Requests\AuthorizationRequestReponseMode;
use Darkink\AuthorizationServer\Policy;
use Darkink\AuthorizationServer\Services\KeyHelperService;
use DateTimeImmutable;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class UserAuthorizationController
{

    protected KeyHelperService $keyHelperService;

    public function __construct(KeyHelperService $keyHelperService)
    {
        $this->keyHelperService = $keyHelperService;
    }

    public function index(AuthorizationRequest $request)
    {
        switch ($request->response_mode) {
            case AuthorizationRequestReponseMode::DECISION->value:
                $permissions = $this->getPermissions($request);
                return response()->json([
                    'valid' => $permissions->contains($request->permission)
                ], 200);
                break;
            case AuthorizationRequestReponseMode::PERMISSIONS->value:
                $privateKey = $this->keyHelperService->getPrivateKey();

                $configuration = Configuration::forAsymmetricSigner(
                    new Sha256(),
                    InMemory::file($privateKey->getKeyPath()),
                    InMemory::base64Encoded($privateKey->getPassPhrase() ?? '')
                );

                $now = new DateTimeImmutable(date("Y-m-d H:i:s"));

                $builder = $configuration->builder();
                $builder->issuedBy(Policy::$issuer)
                    ->issuedAt($now)
                    ->expiresAt($now->modify('+5 minutes'))
                    ->relatedTo($request->user()->id)
                    ->withClaim('roles', $this->getRoles($request))
                    ->withClaim('permissions', $this->getPermissions($request));

                $token = $builder->getToken($configuration->signer(), $configuration->signingKey());

                return response()->json([
                    'token_type' => 'Policy',
                    'expire_in' => 900, //change
                    'policy_token' => $token->toString()
                ], 200);
            case AuthorizationRequestReponseMode::ANALYSE->value:
                throw new Error('NotImplemented');
        }
    }

    protected function getRoles(Request $request)
    {
        return $request->user()->roles->map(function ($p) {
            return $p->name;
        });
    }

    protected function getPermissions(Request $request): Collection
    {
        return $request->user()->permissions();
    }
}
