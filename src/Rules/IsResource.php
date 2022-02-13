<?php

namespace Darkink\AuthorizationServer\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\DatabaseRule;
use phpDocumentor\Reflection\PseudoTypes\False_;

class IsResource extends IsModelRule implements Rule
{

    use DatabaseRule;

    protected bool $nullable;

    public function __construct(bool $nullable = false)
    {
        $this->nullable = $nullable;
    }

    public function passes($attribute, $value)
    {
        if ($this->nullable && $value == null) {
            return true;
        }

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
