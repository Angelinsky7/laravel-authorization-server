<?php

namespace Darkink\AuthorizationServer\Http\Resources;

use Darkink\AuthorizationServer\Services\DiscoverService;
use DateInterval;
use DateTimeImmutable;
use Error;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use TypeError;

class AuthorizationResource extends JsonResource
{

    public static $wrap = '';
    public bool $json_mode;

    public function __construct($resource, bool $json_mode = false)
    {
        parent::__construct($resource);
        $this->json_mode = $json_mode;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        /** @var DiscoverService $discovery */
        $discovery = App::make(DiscoverService::class);
        $result = parent::toArray($request);

        $jti = $this->generateUniqueIdentifier();
        $iat = new DateTimeImmutable();
        $exp = $iat->add(new DateInterval("PT{$discovery->tokenExpiration}S"));

        $header = [
            'typ' => 'JWT',
            'alg' => $discovery->alg
        ];

        $payload = array_merge([
            'iss' => $discovery->host,
            'jti' => $jti,
            'iat' => $iat->format('U'),
            'nbf' => $iat->format('U'),
            'exp' => $exp->format('U'),
        ], $result);

        $encoder = new JoseEncoder();
        $encodedHeaders = $this->encode($header, $encoder);
        $encodedPayload  = $this->encode($payload, $encoder);
        $encodedSign = $this->sign($encodedHeaders, $encodedPayload, $discovery->getPrivateKey(), $encoder);

        return $this->json_mode ?
            [
                'header' => $header,
                'payload' => $payload,
                'sign' => $encodedSign
            ] :
            [
                'token_type' => 'Permission',
                'expires_in' => $discovery->tokenExpiration,
                'permission_token' => $encodedHeaders . '.' . $encodedPayload . '.' . $encodedSign
            ];
    }

    protected function generateUniqueIdentifier($length = 40)
    {
        try {
            return \bin2hex(\random_bytes($length));
        } catch (TypeError $e) {
            throw OAuthServerException::serverError('An unexpected error has occurred', $e);
        } catch (Error $e) {
            throw OAuthServerException::serverError('An unexpected error has occurred', $e);
        } catch (Exception $e) {
            // If you get this message, the CSPRNG failed hard.
            throw OAuthServerException::serverError('Could not generate a random string', $e);
        }
    }

    protected function sign(string $encodedHeaders, string $encodedPayload, CryptKey $privateKey, JoseEncoder $encoder)
    {
        $configuration = Configuration::forAsymmetricSigner(
            // You may use RSA or ECDSA and all their variations (256, 384, and 512) and EdDSA over Curve25519
            new Sha256(),
            InMemory::file($privateKey->getKeyPath()),
            InMemory::base64Encoded($privateKey->getPassPhrase() ?? '')
            // You may also override the JOSE encoder/decoder if needed by providing extra arguments here
        );
        $signer = $configuration->signer();
        $key = $configuration->signingKey();

        $sign = $signer->sign($encodedHeaders . '.' . $encodedPayload, $key);
        $encodedSignature = $encoder->base64UrlEncode($sign);

        return $encodedSignature;
    }

    protected function encode(array $items, $encoder): string
    {
        return $encoder->base64UrlEncode(
            $encoder->jsonEncode($items)
        );
    }
}
