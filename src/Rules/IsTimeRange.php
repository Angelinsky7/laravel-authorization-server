<?php

namespace Darkink\AuthorizationServer\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\DatabaseRule;
use phpDocumentor\Reflection\PseudoTypes\False_;

class IsTimeRange extends IsModelRule implements Rule
{
    use DatabaseRule;

    protected int | null $min;
    protected int | null $max;

    public function __construct(int | null $min = null, int | null $max = null)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function passes($attribute, $value)
    {
        $validator = Validator::make([
            'timerange' => $value
        ], [
            'timerange' => 'array:from,to',
            'timerange.from' => 'nullable|required_with:timerange.to|integer|lt:timerange.to',
            'timerange.to' => 'nullable|required_with:timerange.from|integer|gt:timerange.from'
        ]);

        if ($this->min != null) {
            $validator->sometimes(['timerange.from', 'timerange.to'], [
                "min:$this->min"
            ], fn ($p) => true);
        }

        if ($this->max != null) {
            $validator->sometimes(['timerange.from', 'timerange.to'], [
                "max:$this->max"
            ], fn ($p) => true);
        }

        return $validator->fails() === false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $result = 'The :attribute is not a valid timerange.';
        if ($this->min != null) {
            $result = $result . " (min: $this->min";
            $result = $result . ($this->max == null ? ')' : ', ');
        }
        if ($this->max != null) {
            if ($this->min == null) {
                $result = $result . " (";
            }
            $result = $result . "max: $this->max)";
        }
        return $result;
    }
}
