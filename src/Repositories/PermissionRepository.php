<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\Scope;
use Darkink\AuthorizationServer\Models\ScopePermission;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;

class PermissionRepository
{
    public function find(int $id): Permission
    {
        $permission = Policy::permission();
        return $permission->where($permission->id, $id)->first();
    }

    public function gets()
    {
        return Policy::permission()->with('permission');
    }

    public function createScope(string $name, string $description, DecisionStrategy $decision_strategy, Resource $resource, array $scopes): ScopePermission
    {
        DB::beginTransaction();

        try {

            $parent = Policy::permission()->forceFill([
                'name' => $name,
                'description' => $description,
                'decision_strategy' => $decision_strategy->value,
                'discriminator' => 'null'
            ]);
            $parent->save();

            $permission = Policy::scopePermission()->forceFill([
                'id' => $parent->id,
            ]);

            $permission->resource()->associate($resource);
            $permission->scopes()->saveMany($scopes);
            $permission->parent()->save($parent);
            $permission->save();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $permission;
    }

    public function updateScope(ScopePermission $permission, string $name, string $description, DecisionStrategy $decision_strategy, Resource $resource, array $scopes): ScopePermission
    {
        DB::beginTransaction();

        try {
            $permission->parent->forceFill([
                'name' => $name,
                'description' => $description,
                'decision_strategy' => $decision_strategy->name,
            ]);
            $permission->parent->save();

            $permission->resource()->associate($resource);
            $permission->scopes()->saveMany($scopes);
            $permission->save();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $permission;
    }

    public function delete(Permission $permission)
    {
        $permission->delete();
    }
}
