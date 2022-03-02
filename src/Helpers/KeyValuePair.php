<?php

namespace Darkink\AuthorizationServer\Helpers;

class KeyValuePair
{
    public mixed $key;
    public mixed $value;

    public function __construct(mixed $key, mixed $value){
        $this->key = $key;
        $this->value = $value;
    }

}
