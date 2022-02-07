<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Scope;
use Darkink\AuthorizationServer\Policy;

class ScopeRepository
{
    public function find(int $id): Scope
    {
        $scope = Policy::scope();
        return $scope->where($scope->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::scope();
    }

    public function create(string $name, string $displayName, string | null $iconUri): Scope
    {
        $scope = Policy::scope()->forceFill([
            'name' => $name,
            'display_name' => $displayName,
            'icon_uri' => $iconUri,
        ]);

        $scope->save();

        return $scope;
    }

    public function update(Scope $scope, string $name, string $displayName, string | null $iconUri): Scope
    {
        $scope->forceFill([
            'name' => $name,
            'display_name' => $displayName,
            'icon_uri' => $iconUri,
        ])->save();

        return $scope;
    }

    public function delete(Scope $scope)
    {
        $scope->delete();
    }
}
