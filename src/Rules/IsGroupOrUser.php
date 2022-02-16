<?php

namespace Darkink\AuthorizationServer\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\DatabaseRule;

class IsGroupOrUser extends IsModelRule implements Rule
{
    use DatabaseRule;

    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $id = $this->getId($value, 'id');

        $validator = Validator::make([
            'id' => $id
        ], [
            'id' => function ($attribute, $value) {
                if ((new IsGroup())->passes($attribute, $value)) {
                    return true;
                }
                return (new IsUser())->passes($attribute, $value);
            }
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
        return 'The :attribute is not a valid user.';
    }
}
