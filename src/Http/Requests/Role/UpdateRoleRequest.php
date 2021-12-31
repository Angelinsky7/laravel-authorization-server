<?php

namespace Darkink\AuthorizationServer\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        Log::debug('this', [$this]);

        return [
            'name' => ['required', Rule::unique('roles')->ignore($this->role), 'string', 'max:255'],
            'label' => 'required|string|max:255',
            'description' => 'nullable|string'
        ];
    }
}
