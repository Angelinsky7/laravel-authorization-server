<?php

namespace Darkink\AuthorizationServer\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\DatabaseRule;

class AfterOrNull implements Rule, DataAwareRule
{

    public string $beforeAttribute;
    protected $data = [];

    public function __construct($beforeAttribute)
    {
        $this->beforeAttribute = $beforeAttribute;
    }

    public function passes($attribute, $value)
    {
        if ($this->data[$this->beforeAttribute] == null) {
            return true;
        }

        $validator = Validator::make([
            'test' => $value
        ], [
            'test' => "after:$this->beforeAttribute",
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
        return "The :attribute must be a date after $this->beforeAttribute";
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}
