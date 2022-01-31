<?php

namespace Darkink\AuthorizationServer\Models;

/**
 * @obsolete
 * @property Client[] $clients
 */
class ClientPolicy extends Policy
{
    public function __construct()
    {
        $this->table = config('policy.storage.database.prefix') . 'client_policies';
    }
}
