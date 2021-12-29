<?php

namespace Darkink\AuthorizationServer\Http\Controllers;

use Darkink\AuthorizationServer\Policy;
use Error;
use Illuminate\Http\Request;

class DiscoverController
{

    private $_publicKeyType = [
        0 => 'rsa',
        1 => 'dsa',
        3 => 'dh',
        4 => 'ec'
    ];

    public function index(Request $request)
    {
        $host = $request->getSchemeAndHttpHost();

        return [
            'issuer' => $host, //TODO(demarco): Could be an issuer field
            'jwks_uri' => route('policy.discovery.jwks'),
            'role_endpoint' => route('api.policy.role.index'),
            'permission_endpoint' => route('api.policy.permission.index'),
            'authorization_endpoint' => route('api.policy.authorization.index'),

            //TODO(demarco): Should be parameterized AND used in the application
            // {
            'claims_supported' => [
                'sub'
            ],
            'response_modes_supported' => [
                'form_post',
            ],
            'id_token_signing_alg_values_supported'  => [
                'RS256'
            ],
            'subject_types_supported' => [
                'public'
            ],
            'code_challenge_methods_supported' => [
                'plain',
                'S256'
            ],
            'request_parameter_supported'    => true
            // }
        ];
    }

    public function jwks()
    {
        $publicKeyPath = Policy::keyPath('policy-public.key');
        $publicKeyContent = file_get_contents($publicKeyPath);
        $publicKey = openssl_pkey_get_public($publicKeyContent);
        if (!$publicKey) {
            throw new Error('Cannot read public key');
        }
        $publicKeyInfo = openssl_pkey_get_details($publicKey);
        if (!$publicKeyInfo) {
            throw new Error('Cannot read public key');
        }

        $type = $this->_publicKeyType[$publicKeyInfo['type']];
        $hash = strtoupper($this->_base64EncodeUrl(hash('MD5', $publicKeyInfo['key'])));

        return [
            'keys' => [
                0 => [
                    'kty' => strtoupper($type),
                    'use' => 'sig',
                    'kid' => $hash,
                    'e' => $this->_base64EncodeUrl($publicKeyInfo[$type]['e']),
                    'n' => $this->_base64EncodeUrl($publicKeyInfo[$type]['n']),
                    'alg' => 'RS256',
                ]
            ]
        ];
    }

    private function _base64EncodeUrl($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
