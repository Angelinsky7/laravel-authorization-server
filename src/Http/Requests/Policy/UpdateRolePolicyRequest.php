<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Illuminate\Validation\Rule;

class UpdateRolePolicyRequest extends StoreRolePolicyRequest
{
    use RequestPolicyTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            $this->policy_update_rules()
        );
    }
}
