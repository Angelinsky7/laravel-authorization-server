<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Darkink\AuthorizationServer\Rules\IsRole;

class StoreRolePolicyRequest extends StorePolicyRequest
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
                'roles' => ['required', 'array', 'min:1'],
                'roles.*' => ['required', 'distinct', new IsRole()],
            ]
        );
    }
}
