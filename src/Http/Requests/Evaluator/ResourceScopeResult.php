<?php

namespace Darkink\AuthorizationServer\Http\Requests;

class ResourceScopeResult
{
    public string $permission;
    public string $resource;
    public string $scope;

    public int $grantedCount = 0;
    public int $deniedCount = 0;
    public bool $granted = false;

    public function __construct(string $permission, string $resource, string $scope)
    {
    }
}
