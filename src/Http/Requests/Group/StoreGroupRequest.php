<?php

namespace Darkink\AuthorizationServer\Http\Requests\Group;

use Darkink\AuthorizationServer\Rules\IsGroup;
use Darkink\AuthorizationServer\Rules\IsGroupOrUser;
use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:uma_groups|string|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'memberofs' => ['nullable', 'array'],
            'memberofs.*' => ['required', 'distinct', new IsGroup('g')],
            'members' => ['nullable', 'array'],
            'members.*' => ['required', 'distinct', new IsGroupOrUser('g', 'u')],
        ];
    }

}
