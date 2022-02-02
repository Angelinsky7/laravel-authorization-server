<?php

namespace Darkink\AuthorizationServer\Http\Requests\Permission;

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
                'resource' => 'required|unique:uma_resources,id',
                'scopes' => 'nullable',
            ]
        );
    }
}
