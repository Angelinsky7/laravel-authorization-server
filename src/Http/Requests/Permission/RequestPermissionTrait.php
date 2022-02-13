<?php

namespace Darkink\AuthorizationServer\Http\Requests\Permission;

use Illuminate\Validation\Rule;

trait RequestPermissionTrait
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function permission_update_rules()
    {
        return [
            'id' => 'required|exists:uma_permissions,id',
            'name' => ['required', Rule::unique('uma_permissions')->ignore($this->permission), 'string', 'max:255'],
        ];
    }
}
