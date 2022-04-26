<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\ResourcePermission;
use Darkink\AuthorizationServer\Models\ScopePermission;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;

class ResourcePermissionRepository
{
    protected PermissionRepository $permisisonRepository;

    public function __construct(PermissionRepository $permisisonRepository)
    {
        $this->permisisonRepository = $permisisonRepository;
    }

    public function find(int $id): ResourcePermission
    {
        $permission = Policy::resourcePermission();
        return $permission->where($permission->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::resourcePermission()->with('parent');
    }

    //TODO(demarco): This method has some properties that are like the other one in ScopePermissionRepository
    protected function resolve(Resource | int | null $resource)
    {
        //TODO(demarco): this is stupid 5 lines later we use only the ids...
        // {
        if ($resource != null) {
            $resource = is_int($resource) ? Policy::resource()->find($resource) : $resource;
        }
        // }

        return [
            'resource' => $resource,
        ];
    }

    public function create(string $name, string $description, DecisionStrategy | int $decision_strategy, bool $is_system, mixed $policies, string | null $resource_type, Resource | int | null $resource): ResourcePermission
    {
        DB::beginTransaction();

        try {

            //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($resource));

            $parent = $this->permisisonRepository->create($name, $description, $decision_strategy, $is_system, $policies);

            $permission = Policy::resourcePermission()->forceFill([
                'id' => $parent->id,
            ]);
            $permission->parent()->save($parent);
            $permission->resource_type = $resource_type;
            $permission->resource()->associate($resource);
            $permission->save();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $permission;
    }

    public function update(ResourcePermission $permission, string $name, string $description, DecisionStrategy | int $decision_strategy, bool $is_system, mixed $policies, string | null $resource_type, Resource | int | null $resource): ResourcePermission
    {
        DB::beginTransaction();

        try {

            //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($resource));

            $this->permisisonRepository->update($permission->parent, $name, $description, $decision_strategy, $is_system, $policies);

            $permission->resource_type = $resource_type;
            $permission->resource()->associate($resource);
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
    }
}
