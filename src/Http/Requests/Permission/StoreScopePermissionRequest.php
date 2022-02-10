<?php

namespace Darkink\AuthorizationServer\Http\Requests\Permission;

use Darkink\AuthorizationServer\Rules\IsResource;
use Darkink\AuthorizationServer\Rules\IsScope;
use Illuminate\Foundation\Http\FormRequest;

class StoreScopePermissionRequest extends StorePermissionRequest
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
                'resource' => ['required', new IsResource()],
                'scopes' => 'required|array',
                'scopes.*' => ['required', 'distinct', new IsScope()],
            ]
        );
    }
}
