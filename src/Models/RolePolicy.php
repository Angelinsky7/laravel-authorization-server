<?php

namespace Darkink\AuthorizationServer\Models;

/**
 * @property Role[] $roles
 */
class RolePolicy extends Policy
{
    public function __construct()
    {
        $this->table = config('policy.storage.database.prefix') . 'role_policies';
    }
}
