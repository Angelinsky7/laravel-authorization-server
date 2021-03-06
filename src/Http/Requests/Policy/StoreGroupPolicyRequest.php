<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Darkink\AuthorizationServer\Rules\IsGroup;

class StoreGroupPolicyRequest extends StorePolicyRequest
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
                'groups' => ['required', 'array', 'min:1'],
                'groups.*' => ['required', 'distinct', new IsGroup('g')],
            ]
        );
    }
}
