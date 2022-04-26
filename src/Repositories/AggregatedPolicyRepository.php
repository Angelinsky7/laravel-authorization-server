<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\AggregatedPolicy;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Policy as ModelsPolicy;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use function PHPSTORM_META\exitPoint;

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

    protected function checkCircualReferences(ModelsPolicy $policy, array &$visitedPolicies = null)
    {
        if ($visitedPolicies == null) {
            $visitedPolicies = [];
        }

        if (in_array($policy->id, $visitedPolicies)) {
            return false;
        }

        $visitedPolicies[] = $policy->id;

        if ($policy instanceof AggregatedPolicy) {
            foreach ($policy->policies as $child) {
                if (!$this->checkCircualReferences($child->policy->parent, $visitedPolicies)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function checkValidation(ModelsPolicy $policy)
    {
        if (!$this->checkCircualReferences($policy)) {
            $error = ValidationException::withMessages([
                'policies' => ['The Policy has a cyclique dependency tree.'],
            ]);
            throw $error;
        }
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

    public function create(string $name, string $description, PolicyLogic | int $logic, mixed $permissions, DecisionStrategy | int $decision_strategy, bool $is_system, mixed $policies): AggregatedPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($decision_strategy, $policies));

            $parent = $this->policyRepository->create($name, $description, $logic, $is_system, $permissions);

            $policy = Policy::aggregatedPolicy()->forceFill([
                'id' => $parent->id,
            ]);
            $policy->parent()->save($parent);
            $policy->decision_strategy = $decision_strategy->value;
            $policy->save();

            $policy->policies()->saveMany($policies);

            $this->checkValidation($policy->parent);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function update(AggregatedPolicy $policy, string $name, string $description, PolicyLogic | int $logic, mixed $permissions, DecisionStrategy | int $decision_strategy, bool $is_system, mixed $policies): AggregatedPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($decision_strategy, $policies));

            $this->policyRepository->update($policy->parent, $name, $description, $logic, $is_system, $permissions);
            $policy->decision_strategy = $decision_strategy->value;
            $policy->save();

            /** @var \Illuminate\Support\Collection $policies */
            $policy->policies()->sync(is_array($policies) ? $policies : $policies->map(fn ($p) => $p->id)->toArray());

            $this->checkValidation($policy->parent);
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
