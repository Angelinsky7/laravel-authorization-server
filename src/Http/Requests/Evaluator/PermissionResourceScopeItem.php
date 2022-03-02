<?php

namespace Darkink\AuthorizationServer\Http\Requests\Evaluator;

class PermissionResourceScopeItem
{
    public string $resourceName;
    public string | null $scopeName;

    public function __construct(string $resourceName,  string | null $scopeName = null)
    {
        $this->resourceName = $resourceName;
        $this->scopeName = $scopeName;
    }
}
