<?php

namespace Darkink\AuthorizationServer\Http\Requests\Role;

use Darkink\AuthorizationServer\Rules\IsRole;
use Darkink\AuthorizationServer\Rules\IsRoleCyclicParent;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:uma_roles|string|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parents' => ['nullable', 'array'],
            'parents.*' => ['required', 'distinct', new IsRole()],
        ];
    }

    public function validated()
    {
        $result = parent::validated();



        return $result;
    }

}
