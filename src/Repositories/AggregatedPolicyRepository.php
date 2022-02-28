<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\AggregatedPolicy;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;

class AggregatedPolicyRepository
{
    protected PolicyRepository $policyRepository;

    public function __construct(PolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
    }

    public function find(int $id): AggregatedPolicy
    {
        $policy = Policy::aggregatedPolicy();
        return $policy->where($policy->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::aggregatedPolicy()->with('parent');
    }

    protected function resolve(DecisionStrategy | int $decision_strategy, mixed $policies)
    {
        //TODO(demarco): this is stupid 5 lines later we use only the ids...
        // {
        $decision_strategy = is_int($decision_strategy) ? DecisionStrategy::tryFrom($decision_strategy) : $decision_strategy;

        if (count($policies) != 0 && !is_object($policies[0])) {
            $policies = Policy::policy()::all()->whereIn(Policy::user()->getKeyName(), $policies);
        }
        // }

        return [
            'decision_strategy' => $decision_strategy,
            'policies' => $policies
        ];
    }

    public function create(string $name, string $description, PolicyLogic | int $logic, mixed $permissions, DecisionStrategy | int $decision_strategy, mixed $policies): AggregatedPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($decision_strategy, $policies));

            $parent = $this->policyRepository->create($name, $description, $logic, $permissions);

            $policy = Policy::aggregatedPolicy()->forceFill([
                'id' => $parent->id,
            ]);
            $policy->parent()->save($parent);
            $policy->decision_strategy = $decision_strategy->value;
            $policy->save();

            $policy->policies()->saveMany($policies);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function update(AggregatedPolicy $policy, string $name, string $description, PolicyLogic | int $logic, mixed $permissions, DecisionStrategy | int $decision_strategy, mixed $policies): AggregatedPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($decision_strategy, $policies));

            $this->policyRepository->update($policy->parent, $name, $description, $logic, $permissions);
            $policy->decision_strategy = $decision_strategy->value;
            $policy->save();

            /** @var \Illuminate\Support\Collection $policies */
            $policy->policies()->sync(is_array($policies) ? $policies : $policies->map(fn ($p) => $p->id)->toArray());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function delete(AggregatedPolicy $policy)
    {
        $this->policyRepository->delete($policy->parent);
    }
}
