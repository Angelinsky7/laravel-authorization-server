<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Role;
use Darkink\AuthorizationServer\Models\RolePolicy;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;

class RolePolicyRepository
{
    protected PolicyRepository $policyRepository;

    public function __construct(PolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
    }

    public function find(int $id): RolePolicy
    {
        $policy = Policy::rolePolicy();
        return $policy->where($policy->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::rolePolicy()->with('parent');
    }

    protected function resolve(mixed $roles)
    {
        //TODO(demarco): this is stupid 5 lines later we use only the ids...
        // {
        if (count($roles) != 0 && !is_object($roles[0])) {
            $roles = Policy::role()::all()->whereIn(Policy::role()->getKeyName(), $roles);
        }
        // }

        return [
            'roles' => $roles,
        ];
    }

    public function create(string $name, string $description, PolicyLogic | int $logic, bool $is_system, mixed $permissions, mixed $roles): RolePolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($roles));

            $parent = $this->policyRepository->create($name, $description, $logic, $is_system, $permissions);

            $policy = Policy::rolePolicy()->forceFill([
                'id' => $parent->id,
            ]);
            $policy->parent()->save($parent);
            $policy->save();
            $policy->roles()->saveMany($roles);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function update(RolePolicy $policy, string $name, string $description, PolicyLogic | int $logic, bool $is_system, mixed $permissions, mixed $roles): RolePolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($roles));

            $this->policyRepository->update($policy->parent, $name, $description, $logic, $is_system, $permissions);
            $policy->save();

            /** @var \Illuminate\Support\Collection $roles */
            $policy->roles()->sync(is_array($roles) ? $roles : $roles->map(fn ($p) => $p->id)->toArray());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function delete(RolePolicy $policy)
    {
        $this->policyRepository->delete($policy->parent);
    }
}
