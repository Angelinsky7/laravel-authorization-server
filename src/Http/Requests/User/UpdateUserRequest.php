<?php

namespace Darkink\AuthorizationServer\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends StoreUserRequest
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
                'id' => 'required|exists:users,id',
                'name' => ['required', Rule::unique('users')->ignore($this->user), 'string', 'max:255'],
                'email' => ['required', Rule::unique('users')->ignore($this->user), 'email', 'max:255'],
                'password' => ['nullable', 'confirmed', Password::defaults()],
                // 'password_confirmation' => ['required_with:password', Password::defaults()],
            ]
        );
    }
}
