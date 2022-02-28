<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Rules\IsPolicy;
use Illuminate\Validation\Rules\Enum;

class StoreAggregatedPolicyRequest extends StorePolicyRequest
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
                'decision_strategy' => ['required', new Enum(DecisionStrategy::class)],
                'policies' => ['required', 'array', 'min:1'],
                'policies.*' => ['required', 'distinct', new IsPolicy()],
            ]
        );
    }
}
