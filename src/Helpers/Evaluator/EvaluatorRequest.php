<?php

namespace Darkink\AuthorizationServer\Helpers\Evaluator;

use Darkink\AuthorizationServer\Models\Client;

class EvaluatorRequest
{

    public Client $client;
    public array | null $permissions;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}