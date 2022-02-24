<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Darkink\AuthorizationServer\Rules\IsResource;
use Darkink\AuthorizationServer\Rules\IsScope;
use Illuminate\Foundation\Http\FormRequest;

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
                // 'resource_type' => ['required_if:resource,null'],
                // 'resource' => ['required_if:resource_type,null', new IsResource(true)],
            ]
        );
    }
}
