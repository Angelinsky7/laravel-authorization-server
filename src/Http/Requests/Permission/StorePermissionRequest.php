<?php

namespace Darkink\AuthorizationServer\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Darkink\AuthorizationServer\Models\DecisionStrategy;

class StorePermissionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:uma_permissions|string|max:255',
            'description' => 'required|string',
            'decision_strategy' => ['required', new Enum(DecisionStrategy::class)]
        ];
    }
}
