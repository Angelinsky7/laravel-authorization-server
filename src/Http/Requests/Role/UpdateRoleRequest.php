<?php

namespace Darkink\AuthorizationServer\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends StoreRoleRequest
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
                'id' => 'required|exists:uma_roles,id',
                'name' => ['required', Rule::unique('uma_roles')->ignore($this->role), 'string', 'max:255'],
            ]
        );
    }
}
