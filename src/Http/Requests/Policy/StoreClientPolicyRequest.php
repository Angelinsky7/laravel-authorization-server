<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Darkink\AuthorizationServer\Rules\IsClient;

class StoreClientPolicyRequest extends StorePolicyRequest
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
                'clients' => ['required', 'array', 'min:1'],
                'clients.*' => ['required', 'distinct', new IsClient()],
            ]
        );
    }
}
