<?php

namespace Darkink\AuthorizationServer\Http\Requests\User;

use Darkink\AuthorizationServer\Rules\IsGroup;
use Darkink\AuthorizationServer\Rules\IsRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:users|string|max:255',
            'email' => 'required|unique:users|email|max:255',
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['required', 'distinct', new IsRole()],
            'memberofs' => ['nullable', 'array'],
            'memberofs.*' => ['required', 'distinct', new IsGroup('g')],
        ];
    }
}
