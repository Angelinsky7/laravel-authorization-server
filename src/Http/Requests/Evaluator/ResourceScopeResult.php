<?php

namespace Darkink\AuthorizationServer\Http\Requests\Evaluator;

use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\Scope;

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

    public static function getHash(Permission $permission, Resource $resource, Scope $scope): int
    {
        return 1610612741 * self::getHashCode($permission->id) + 805306457 * self::getHashCode($resource->id) + 402653189 * self::getHashCode($scope->id);
    }

    private static function getHashCode($src)
    {
        $hash = hash('md5', $src);
        $result = base_convert($hash, 16, 10);
        return $result;
    }
}
