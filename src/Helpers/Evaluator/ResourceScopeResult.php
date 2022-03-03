<?php

namespace Darkink\AuthorizationServer\Helpers\Evaluator;

use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\Scope;

class ResourceScopeResult
{
    public Permission $permission;
    public Resource $resource;
    public Scope $scope;

    public int $granted_count = 0;
    public int $denied_count = 0;
    public bool | null $granted = null;

    public function __construct(Permission $permission, Resource $resource, Scope $scope)
    {
        $this->permission = $permission;
        $this->resource = $resource;
        $this->scope = $scope;
    }

    public static function getHash(Permission $permission, Resource $resource, Scope $scope): string
    {
        $hash = 1610612741 * self::getHashCode($permission->id) + 805306457 * self::getHashCode($resource->id) + 402653189 * self::getHashCode($scope->id);
        $result = "h$hash";
        return $result;
    }

    private static function getHashCode($src)
    {
        $hash = hash('md5', $src);
        $result = base_convert($hash, 16, 10);
        return $result;
    }
}
