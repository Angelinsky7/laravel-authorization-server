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
    protected PermissionRepository $permisisonRepository;

    public function __construct(PermissionRepository $permisisonRepository)
    {
        $this->permisisonRepository = $permisisonRepository;
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

    //TODO(demarco): This method has some properties that are like the other one in ResourcePermissionRepository
    protected function resolve(Resource | int $resource, array $scopes)
    {
        //TODO(demarco): this is stupid 5 lines later we use only the ids...
        // {
        $resource = is_int($resource) ? Policy::resource()->find($resource) : $resource;

        if (count($scopes) != 0 && !is_object($scopes[0])) {
            $scopes = Policy::scope()::all()->whereIn(Policy::scope()->getKeyName(), $scopes);
        }
        // }

        return [
            'resource' => $resource,
            'scopes' => $scopes
        ];
    }

    public function create(string $name, string $description, DecisionStrategy | int $decision_strategy, mixed $policies, Resource | int $resource, mixed $scopes): ScopePermission
    {
        DB::beginTransaction();

        try {

            //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($resource, $scopes));

            $parent = $this->permisisonRepository->create($name, $description, $decision_strategy, $policies);

            $permission = Policy::scopePermission()->forceFill([
                'id' => $parent->id,
            ]);
            $permission->parent()->save($parent);
            $permission->resource()->associate($resource);
            $permission->save();
            $permission->scopes()->saveMany($scopes, $scopes->map(fn ($p) => ['resource_id' => $resource->id])->toArray());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $permission;
    }

    public function update(ScopePermission $permission, string $name, string $description, DecisionStrategy | int $decision_strategy, mixed $policies, Resource | int $resource, mixed $scopes): ScopePermission
    {
        DB::beginTransaction();

        try {

            //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($resource, $scopes));

            $this->permisisonRepository->update($permission->parent, $name, $description, $decision_strategy, $policies);
            if ($resource->id != $permission->resource->id) {
                $permission->scopes()->sync([]);
            }

            $permission->resource()->associate($resource);
            $permission->save();

            /** @var \Illuminate\Support\Collection $scopes */
            $permission->scopes()->syncWithPivotValues($scopes->map(fn ($p) => $p->id)->toArray(), ['resource_id' => $resource->id]);
            // $permission->save();
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
