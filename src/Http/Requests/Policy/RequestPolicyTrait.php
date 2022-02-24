<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Illuminate\Validation\Rule;

trait RequestPolicyTrait
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function policy_update_rules()
    {
        return [
            'id' => 'required|exists:uma_policies,id',
            'name' => ['required', Rule::unique('uma_policies')->ignore($this->policy), 'string', 'max:255'],
        ];
    }
}
