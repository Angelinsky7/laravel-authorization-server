<?php

namespace Darkink\AuthorizationServer\Http\Requests\Scope;

use Illuminate\Foundation\Http\FormRequest;

class StoreScopeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:uma_scopes|string|max:255',
            'display_name' => 'required|string|max:255',
            'icon_uri' => 'nullable|string',
        ];
    }
}
