<?php

namespace Darkink\AuthorizationServer\Http\Requests\Scope;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateScopeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', Rule::unique('uma_scopes')->ignore($this->scope), 'string', 'max:255'],
            'display_name' => 'required|string|max:255',
            'icon_uri' => 'nullable|string',
        ];
    }
}
