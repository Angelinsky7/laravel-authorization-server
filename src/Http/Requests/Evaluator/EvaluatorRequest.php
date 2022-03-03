<?php

namespace Darkink\AuthorizationServer\Http\Requests\Evaluator;

use Darkink\AuthorizationServer\Rules\IsUserSameAsRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class EvaluatorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_id' => ['required', 'string', new IsUserSameAsRequest()],
            'response_mode' => ['required', new Enum(EvaluatorRequestResponseMode::class)],
            'permission' => 'nullable|string'
        ];
    }

    public function validated()
    {
        $result = parent::validated();
        $result['response_mode'] = EvaluatorRequestResponseMode::tryFrom($result['response_mode']);
        return $result;
    }
}
