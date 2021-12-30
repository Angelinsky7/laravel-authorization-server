<?php

namespace Darkink\AuthorizationServer\Rules;

use Darkink\AuthorizationServer\Services\BearerTokenDecoderService;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\UnencryptedToken;

class CheckClientIdWithToken implements Rule
{

    protected UnencryptedToken $token;

    public function __construct(BearerTokenDecoderService $bearerTokenDecoderService)
    {
        $bearerToken = Request::capture()->bearerToken();
        $this->token = $bearerTokenDecoderService->parse($bearerToken);
    }

    public function passes($attribute, $value)
    {
        if (!$this->token) {
            return false;
        }
        return $value === $this->token->claims()->get('client_id');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'client_id of request and client_id of token are not the same';
    }
}
