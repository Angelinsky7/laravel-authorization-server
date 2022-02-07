<?php

namespace Darkink\AuthorizationServer\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\DatabaseRule;

class IsResource extends IsModelRule implements Rule
{

    use DatabaseRule;

    public function passes($attribute, $value)
    {
        $id = $this->getId($value, 'id');

        $validator = Validator::make([
            'id' => $id
        ], [
            'id' => 'exists:uma_resources,id'
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
        return 'The :attribute is not a valid resource.';
    }
}
