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
    protected ResourceRepository $resourceRepository;
    protected ScopeRepository $scopeRepository;

    public function __construct(ResourceRepository $resourceRepository, ScopeRepository $scopeRepository)
    {
        $this->resourceRepository = $resourceRepository;
        $this->scopeRepository = $scopeRepository;
    }

    public function find(int $id): Permission
    {
        $permission = Policy::permission();
        return $permission->where($permission->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::permission()->with('permission');
    }

    public function create(string $name, string $description, DecisionStrategy | int $decision_strategy): Permission
    {
        $permission = Policy::permission()->forceFill([
            'name' => $name,
            'description' => $description,
            'decision_strategy' => $decision_strategy->value,
            'discriminator' => 'null'
        ]);
        $permission->save();

        return $permission;
    }

    public function update(Permission $permission, string $name, string $description, DecisionStrategy | int $decision_strategy): Permission
    {
        $permission->forceFill([
            'name' => $name,
            'description' => $description,
            'decision_strategy' => $decision_strategy->value,
        ]);
        $permission->save();

        return $permission;
    }

    // public function createScope(string $name, string $description, DecisionStrategy | int $decision_strategy, Resource | int $resource, array $scopes): ScopePermission
    // {
    //     DB::beginTransaction();

    //     try {

    //         $decision_strategy = is_int($decision_strategy) ? DecisionStrategy::tryFrom($decision_strategy) : $decision_strategy;
    //         $resource = is_int($resource) ? $this->resourceRepository->find($resource) : $resource;

    //         if (count($scopes) != 0 && !is_object($scopes[0])) {
    //             $scopes = $this->scopeRepository->gets()->all()->whereIn(Policy::scope()->getKeyName(), $scopes);
    //         }

    //         $parent = Policy::permission()->forceFill([
    //             'name' => $name,
    //             'description' => $description,
    //             'decision_strategy' => $decision_strategy->value,
    //             'discriminator' => 'null'
    //         ]);
    //         $parent->save();

    //         $permission = Policy::scopePermission()->forceFill([
    //             'id' => $parent->id,
    //         ]);
    //         $permission->parent()->save($parent);
    //         $permission->resource()->associate($resource);
    //         $permission->save();

    //         $permission->scopes()->saveMany($scopes);
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }

    //     DB::commit();

    //     return $permission;
    // }

    // public function updateScope(ScopePermission $permission, string $name, string $description, DecisionStrategy $decision_strategy, Resource $resource, array $scopes): ScopePermission
    // {
    //     DB::beginTransaction();

    //     try {
    //         $permission->parent->forceFill([
    //             'name' => $name,
    //             'description' => $description,
    //             'decision_strategy' => $decision_strategy->name,
    //         ]);
    //         $permission->parent->save();

    //         $permission->resource()->associate($resource);
    //         $permission->scopes()->saveMany($scopes);
    //         $permission->save();
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }

    //     DB::commit();

    //     return $permission;
    // }

    public function delete(Permission $permission)
    {
        $permission->delete();
    }
}
