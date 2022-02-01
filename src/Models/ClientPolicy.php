<?php

namespace Darkink\AuthorizationServer\Models;

/**
 * @obsolete
 * @property Client[] $clients
 */
class ClientPolicy extends Policy
{

    protected $table = 'uma_client_policies';

}
