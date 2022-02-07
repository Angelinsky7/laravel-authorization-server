<?php

namespace Darkink\AuthorizationServer\Rules;

use Illuminate\Support\Arr;

class IsModelRule
{
    public function getId($value, $key)
    {
        if (!is_int($value) && !is_string($value)) {
            if (is_array($value) && Arr::exists($value, $key)) {
                $value = $value[$key];
            } else {
                return null;
            }
        }
        return $value;
    }
}
