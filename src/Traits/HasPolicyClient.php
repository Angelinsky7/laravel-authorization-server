<?php

namespace Darkink\AuthorizationServer\Traits;

use Darkink\AuthorizationServer\Models\Client;

trait HasPolicyClient
{
    public function client()
    {
        return $this->hasOne(Client::class, 'oauth_id');
    }
}
