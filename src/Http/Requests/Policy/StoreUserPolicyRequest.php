<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Darkink\AuthorizationServer\Rules\IsUser;

class StoreUserPolicyRequest extends StorePolicyRequest
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
                'users' => ['required', 'array', 'min:1'],
                'users.*' => ['required', 'distinct', new IsUser()],
            ]
        );
    }
}
