<?php

namespace Darkink\AuthorizationServer\Http\Requests\Resource;

use Darkink\AuthorizationServer\Rules\IsScope;
use Illuminate\Validation\Rule;

class UpdateResourceRequest extends StoreResourceRequest
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
                'id' => 'required|exists:uma_resources,id',
                'name' => ['required', Rule::unique('uma_resources')->ignore($this->resource), 'string', 'max:255'],
            ]
        );
    }
}
