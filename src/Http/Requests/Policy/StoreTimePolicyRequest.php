<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Darkink\AuthorizationServer\Rules\IsClient;
use Darkink\AuthorizationServer\Rules\IsTimeRange;

class StoreTimePolicyRequest extends StorePolicyRequest
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
                'not_before' => ['nullable', 'date_format:d/m/Y', 'before:not_after'],
                'not_after' => ['nullable', 'date_format:d/m/Y', 'after:not_before'],
                'day_of_month' => [new IsTimeRange()],
                'month' => [new IsTimeRange()],
                'year' => [new IsTimeRange()],
                'hour' => [new IsTimeRange()],
                'minute' => [new IsTimeRange()]
            ]
        );
    }
}
