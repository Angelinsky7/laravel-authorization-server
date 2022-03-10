<?php

namespace Darkink\AuthorizationServer\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\DatabaseRule;
use phpDocumentor\Reflection\PseudoTypes\False_;

class IsTimeRange extends IsModelRule implements Rule
{

    use DatabaseRule;

    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
    //    $id = $this->getId($value, 'id');

        $validator = Validator::make([
            'timerange' => $value
        ], [
            'timerange' => 'array:from,to'
        ]);

        return $validator->fails() === false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not a valid timerange.';
    }
}