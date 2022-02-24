<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Group;
use Darkink\AuthorizationServer\Models\GroupPolicy;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\ResourcePermission;
use Darkink\AuthorizationServer\Models\ScopePermission;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;

class GroupPolicyRepository
{
    protected PolicyRepository $policyRepository;

    public function __construct(PolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
    }

    public function find(int $id): GroupPolicy
    {
        $policy = Policy::groupPolicy();
        return $policy->where($policy->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::groupPolicy()->with('parent');
    }

    //TODO(demarco): This method has some properties that are like the other one in the other policies
    protected function resolve(mixed $groups)
    {
        //TODO(demarco): this is stupid 5 lines later we use only the ids...
        // {
        if (count($groups) != 0 && !is_object($groups[0])) {
            $groupsWithoutPrefix = array_map(fn ($p) => substr($p, strlen('g')), $groups);
            $groups = Policy::group()::all()->whereIn(Policy::group()->getKeyName(), $groupsWithoutPrefix);
        }
        // }

        return [
            'groups' => $groups,
        ];
    }

    public function create(string $name, string $description, PolicyLogic | int $logic, mixed $permissions, mixed $groups): GroupPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($groups));

            $parent = $this->policyRepository->create($name, $description, $logic, $permissions);

            $policy = Policy::groupPolicy()->forceFill([
                'id' => $parent->id,
            ]);
            $policy->parent()->save($parent);
            $policy->save();
            $policy->groups()->saveMany($groups);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function update(GroupPolicy $policy, string $name, string $description, PolicyLogic | int $logic, mixed $permissions, mixed $groups): GroupPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($groups));

            $this->policyRepository->update($policy->parent, $name, $description, $logic, $permissions);
            $policy->save();

            /** @var \Illuminate\Support\Collection $groups */
            $policy->groups()->sync(is_array($groups) ? $groups : $groups->map(fn ($p) => $p->id)->toArray());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function delete(GroupPolicy $policy)
    {
        $this->policyRepository->delete($policy->parent);
    }
}
