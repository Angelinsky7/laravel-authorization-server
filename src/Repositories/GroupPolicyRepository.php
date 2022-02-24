<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Group;
use Darkink\AuthorizationServer\Models\GroupPolicy;
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

    // //TODO(demarco): This method has some properties that are like the other one in ScopePermissionRepository
    // protected function resolve(DecisionStrategy | int $decision_strategy, Resource | int | null $resource)
    // {
    //     //TODO(demarco): this is stupid 5 lines later we use only the ids...
    //     // {
    //     $decision_strategy = is_int($decision_strategy) ? DecisionStrategy::tryFrom($decision_strategy) : $decision_strategy;
    //     if ($resource != null) {
    //         $resource = is_int($resource) ? $this->resourceRepository->find($resource) : $resource;
    //     }
    //     // }

    //     return [
    //         'decision_strategy' => $decision_strategy,
    //         'resource' => $resource,
    //     ];
    // }

    public function create(string $name): GroupPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            // extract($this->resolve($decision_strategy, $resource));

            $parent = $this->policyRepository->create($name);

            $policy = Policy::groupPolicy()->forceFill([
                'id' => $parent->id,
            ]);
            $policy->parent()->save($parent);
            // $policy->resource_type = $resource_type;
            // $policy->resource()->associate($resource);
            $policy->save();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function update(GroupPolicy $policy, string $name): GroupPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            // extract($this->resolve($decision_strategy, $resource));

            $this->policyRepository->update($policy->parent, $name);

            // $policy->resource_type = $resource_type;
            // $policy->resource()->associate($resource);
            $policy->save();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function delete(ScopePermission $policy)
    {
        $this->permisisonRepository->delete($policy->parent);
    }
}
