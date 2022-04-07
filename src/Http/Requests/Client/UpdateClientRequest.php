<?php

namespace Darkink\AuthorizationServer\Http\Requests\Client;

use Illuminate\Validation\Rule;

class UpdateClientRequest extends StoreClientRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'name' => ['required', Rule::unique('oauth_clients')->ignore($this->client), 'string'],
                // 'client_id' => ['required', Rule::unique('uma_clients')->ignore($this->client, 'oauth_id'), 'string'],
                // 'client_name' => ['required', Rule::unique('uma_clients')->ignore($this->client, 'oauth_id'), 'string'],
                'secret' => 'nullable|string|min:10',
                'password_client' => 'nullable|string|min:10',
            ]
        );
    }
}
