<?php

namespace Darkink\AuthorizationServer\Http\Requests\Policy;

use Darkink\AuthorizationServer\Rules\AfterOrNull;
use Darkink\AuthorizationServer\Rules\BeforeOrNull;
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
                'not_before' => ['nullable', 'date', new BeforeOrNull('not_after')],
                'not_after' => ['nullable', 'date', new AfterOrNull('not_before')],
                'day_of_month' => [new IsTimeRange(1, 31)],
                'month' => [new IsTimeRange(1, 12)],
                'year' => [new IsTimeRange(1900, 2100)],
                'hour' => [new IsTimeRange(0, 23)],
                'minute' => [new IsTimeRange(0, 59)]
            ]
        );
    }
}
