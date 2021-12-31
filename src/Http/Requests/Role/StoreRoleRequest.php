<?php

namespace Darkink\AuthorizationServer\Http\Requests\Role;

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
            'name' => 'required|unique:roles|string|max:255',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string'
        ];
    }
}
