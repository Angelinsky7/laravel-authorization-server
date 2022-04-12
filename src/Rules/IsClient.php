<?php

namespace Darkink\AuthorizationServer\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\DatabaseRule;

class IsClient extends IsModelRule implements Rule
{
    use DatabaseRule;

    public string $prefix;
    public bool $checkOauthId;

    public function __construct(string $prefix = '', bool $checkOauthId = false)
    {
        $this->prefix = $prefix;
        $this->checkOauthId = $checkOauthId;
    }

    public function passes($attribute, $value)
    {
        $id = $this->getId($value, 'id');
        $key =  $this->checkOauthId ? 'oauth_id' : 'id';

        $validator = Validator::make([
            'id' => $id
        ], [
            'id' => "exists:uma_clients,{$key}"
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
        return 'The :attribute is not a valid client.';
    }
}
