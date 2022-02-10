<?php

namespace Darkink\AuthorizationServer\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Rules\IsResource;
use Darkink\AuthorizationServer\Rules\IsScope;
use Illuminate\Validation\Rule;

class UpdateScopePermissionRequest extends StoreScopePermissionRequest
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
                'id' => 'required|exists:uma_permissions,id',
                'name' => ['required', Rule::unique('uma_permissions')->ignore($this->permission), 'string', 'max:255'],
            ]
        );
    }
}
