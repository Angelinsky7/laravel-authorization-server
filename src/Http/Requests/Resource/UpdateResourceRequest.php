<?php

namespace Darkink\AuthorizationServer\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateResourceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', Rule::unique('uma_resources')->ignore($this->resource), 'string', 'max:255'],
            'display_name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'icon_uri' => 'nullable|string',
            'uris' => 'nullable',
            'scopes' => 'nullable'
        ];
    }
}
