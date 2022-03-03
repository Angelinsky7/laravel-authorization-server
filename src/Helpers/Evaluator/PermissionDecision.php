<?php

namespace Darkink\AuthorizationServer\Helpers\Evaluator;

use Darkink\AuthorizationServer\Helpers\KeyValuePair;

class PermissionDecision {

    public bool $result;

    /** @var KeyValuePair[] */
    public array $policies = [];

    public function __construct(bool $result, array $policies)
    {
        $this->result = $result;
        $this->policies = $policies;
    }

}
