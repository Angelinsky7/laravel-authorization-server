<?php

namespace Darkink\AuthorizationServer\Http\Requests\Client;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\PolicyEnforcement;
use Darkink\AuthorizationServer\Rules\IsClient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreClientRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:oauth_clients|string',
            'user_id' => 'nullable',
            'secret' => 'nullable|string|min:10',
            'provider' => 'nullable|string',
            'redirect' => 'required|string',
            'personal_access_client' => 'nullable|bool',
            'password_client' => 'nullable|bool',
            'revoked' => 'nullable|bool',
            'enabled' => 'nullable|bool',
            'client_id' => 'required|string|unique:uma_clients',
            'require_client_secret' => 'nullable|bool',
            'client_name' => 'required|string|unique:uma_clients',
            'description' => 'required|string',
            'client_uri' => 'required|string',
            'policy_enforcement' => ['required', new Enum(PolicyEnforcement::class)],
            'decision_strategy' => ['required', new Enum(DecisionStrategy::class)],
            'analyse_mode_enabled' => 'nullable|bool',
        ];
    }
}
