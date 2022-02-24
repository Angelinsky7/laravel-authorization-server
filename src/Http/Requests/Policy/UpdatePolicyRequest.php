<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Illuminate\Validation\Rule;

class UpdatePolicyRequest extends StorePolicyRequest
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
