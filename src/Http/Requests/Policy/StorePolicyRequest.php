<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Darkink\AuthorizationServer\Rules\IsPermission;

class StorePolicyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:uma_policies|string|max:255',
            'description' => 'required|string',
            'logic' => ['required', new Enum(PolicyLogic::class)],
            'permissions' => 'nullable|array',
            'permissions.*' => ['required', 'distinct', new IsPermission()],
        ];
    }
}
