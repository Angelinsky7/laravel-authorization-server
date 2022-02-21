<?php

namespace Darkink\AuthorizationServer\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\DatabaseRule;

class IsUser extends IsModelRule implements Rule
{
    use DatabaseRule;

    public string $prefix;

    public function __construct(string $prefix = '')
    {
        $this->prefix = $prefix;
    }

    public function passes($attribute, $value)
    {
        $valueWitoutPrefix = $value;

        if ($this->prefix != '') {
            if (!str_starts_with($value, $this->prefix)) {
                return false;
            }
            $valueWitoutPrefix = substr($value, strlen($this->prefix));
        }

        $id = $this->getId($valueWitoutPrefix, 'id');

        $validator = Validator::make([
            'id' => $id
        ], [
            'id' => 'exists:users,id'
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
