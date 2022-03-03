<?php

namespace Darkink\AuthorizationServer\Helpers\Evaluator;

class PermissionResourceScopeItem
{
    public string $resource_name;
    public string | null $scope_name;

    public function __construct(string $resource_name,  string | null $scope_name = null)
    {
        $this->resource_name = $resource_name;
        $this->scope_name = $scope_name;
    }
}
