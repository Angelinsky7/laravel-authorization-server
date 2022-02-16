<?php

namespace Darkink\AuthorizationServer\Http\Requests\Group;

use Illuminate\Validation\Rule;

class UpdateGroupRequest extends StoreGroupRequest
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
                'id' => 'required|exists:uma_groups,id',
                'name' => ['required', Rule::unique('uma_groups')->ignore($this->group), 'string', 'max:255'],
            ]
        );
    }
}
