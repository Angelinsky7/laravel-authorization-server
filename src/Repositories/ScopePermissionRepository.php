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

class ScopePermissionRepository
{
    protected ResourceRepository $resourceRepository;
    protected ScopeRepository $scopeRepository;
    protected PermissionRepository $permisisonRepository;

    public function __construct(PermissionRepository $permisisonRepository, ResourceRepository $resourceRepository, ScopeRepository $scopeRepository)
    {
        $this->permisisonRepository = $permisisonRepository;
        $this->resourceRepository = $resourceRepository;
        $this->scopeRepository = $scopeRepository;
    }

    public function find(int $id): ScopePermission
    {
        $permission = Policy::scopePermission();
        return $permission->where($permission->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::scopePermission()->with('parent');
    }

    public function create(string $name, string $description, DecisionStrategy | int $decision_strategy, Resource | int $resource, array $scopes): ScopePermission
    {
        DB::beginTransaction();

        try {

            $decision_strategy = is_int($decision_strategy) ? DecisionStrategy::tryFrom($decision_strategy) : $decision_strategy;
            $resource = is_int($resource) ? $this->resourceRepository->find($resource) : $resource;

            if (count($scopes) != 0 && !is_object($scopes[0])) {
                $scopes = $this->scopeRepository->gets()->all()->whereIn(Policy::scope()->getKeyName(), $scopes);
            }

            $parent = $this->permisisonRepository->create($name, $description, $decision_strategy);

            // $parent = Policy::permission()->forceFill([
            //     'name' => $name,
            //     'description' => $description,
            //     'decision_strategy' => $decision_strategy->value,
            //     'discriminator' => 'null'
            // ]);
            // $parent->save();

            $permission = Policy::scopePermission()->forceFill([
                'id' => $parent->id,
            ]);
            $permission->parent()->save($parent);
            $permission->resource()->associate($resource);
            $permission->save();

            $permission->scopes()->saveMany($scopes);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $permission;
    }

    public function update(ScopePermission $permission, string $name, string $description, DecisionStrategy $decision_strategy, Resource $resource, array $scopes): ScopePermission
    {
        DB::beginTransaction();

        try {
            $this->permisisonRepository->update($permission->parent, $name, $description, $decision_strategy);
            // $permission->parent->forceFill([
            //     'name' => $name,
            //     'description' => $description,
            //     'decision_strategy' => $decision_strategy->value,
            // ]);
            // $permission->parent->save();

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

    public function delete(ScopePermission $permission)
    {
        $this->permisisonRepository->delete($permission->parent);
        // $permission->delete();
    }
}
