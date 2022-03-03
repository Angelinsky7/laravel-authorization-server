<?php

namespace Darkink\AuthorizationServer\Helpers\Evaluator;

use Darkink\AuthorizationServer\Helpers\KeyValuePair;
use Darkink\AuthorizationServer\Models\Permission;

class EvaluatorResult
{
    /** @var KeyValuePair[] $permission_decision */
    public array $permissions_decisions = [];

    public function addPermission(Permission $permission, PermissionDecision $permission_decision)
    {
        $key = $permission->id;
        if (!array_key_exists($key, $this->permissions_decisions)) {
            $permissions_decisions[$key] = new KeyValuePair($permission, null);
        }
        $permissions_decisions[$key]->value = $permission_decision;
    }
}
