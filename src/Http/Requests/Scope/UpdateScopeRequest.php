<?php

namespace Darkink\AuthorizationServer\Http\Requests\Scope;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateScopeRequest extends StoreScopeRequest
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
                'id' => 'required|exists:uma_scopes,id',
                'name' => ['required', Rule::unique('uma_scopes')->ignore($this->scope), 'string', 'max:255'],
            ]
        );
    }
}
