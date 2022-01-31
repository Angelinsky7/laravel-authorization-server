<?php

namespace Darkink\AuthorizationServer\Models;

/**
 * @property string[] $users
 */
class UserPolicy extends Policy
{
    public function __construct()
    {
        $this->table = config('policy.storage.database.prefix') . 'user_policies';
    }
}
