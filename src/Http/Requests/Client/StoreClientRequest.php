<?php

namespace Darkink\AuthorizationServer\Http\Requests\Client;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\PolicyEnforcement;
use Darkink\AuthorizationServer\Rules\IsClient;
use Darkink\AuthorizationServer\Rules\IsPermission;
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
            'personal_access_client' => 'nullable|boolean',
            'password_client' => 'nullable|boolean',
            'revoked' => 'nullable|boolean',
            'enabled' => 'nullable|boolean',
            'client_id' => 'required|string|unique:uma_clients',
            'require_client_secret' => 'nullable|boolean',
            'client_name' => 'required|string|unique:uma_clients',
            'description' => 'required|string',
            'client_uri' => 'required|string',
            'policy_enforcement' => ['required', new Enum(PolicyEnforcement::class)],
            'decision_strategy' => ['required', new Enum(DecisionStrategy::class)],
            'permission_splitter' => 'required|string|min:1|max:1',
            'analyse_mode_enabled' => 'nullable|boolean',
            'json_mode_enabled' => 'nullable|boolean',
            'all_resources' => 'nullable|boolean',
            'all_scopes' => 'nullable|boolean',
            'all_roles' => 'nullable|boolean',
            'all_groups' => 'nullable|boolean',
            'all_policies' => 'nullable|boolean',
            'all_permissions' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => ['required', 'distinct', new IsPermission()],
        ];
    }
}
