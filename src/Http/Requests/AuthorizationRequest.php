<?php

namespace Darkink\AuthorizationServer\Http\Requests;

use Darkink\AuthorizationServer\Rules\CheckClientIdWithToken;
use Darkink\AuthorizationServer\Services\BearerTokenDecoderService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AuthorizationRequest extends FormRequest
{
    protected $bearerTokenDecoderService;

    public function __construct(BearerTokenDecoderService $bearerTokenDecoderService)
    {
        $this->bearerTokenDecoderService = $bearerTokenDecoderService;
    }

    public function rules()
    {
        $reponseModeKey = AuthorizationRequestReponseMode::DECISION->value;

        return [
            'client_id' => ['required', 'string', new CheckClientIdWithToken($this->bearerTokenDecoderService)],
            'response_mode' => ['required', new Enum(AuthorizationRequestReponseMode::class)],
            'permission' => "string|required_if:response_mode,$reponseModeKey"
        ];
    }
}
