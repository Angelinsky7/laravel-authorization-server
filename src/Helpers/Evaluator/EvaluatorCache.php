<?php

namespace Darkink\AuthorizationServer\Helpers\Evaluator;

use Darkink\AuthorizationServer\Helpers\KeyValuePair;
use Darkink\AuthorizationServer\Models\Policy;
use Error;

class EvaluatorCache
{

    /** @var KeyValuePair[] $policy_result Policy/?bool */
    protected array $policy_result = [];

    public function addPolicyCache(Policy $policy, bool $decision)
    {
        $key = $policy->id;
        if (!array_key_exists($key, $this->policy_result)) {
            $this->policy_result[$key] = new KeyValuePair($policy, null);
        }
        $this->policy_result[$key]->value = $decision;
    }

    public function hasPolicyCache(Policy $policy): bool
    {
        return array_key_exists($policy->id, $this->policy_result);
    }

    public function getPolicyCache(Policy $policy): ?bool
    {
        if ($this->hasPolicyCache($policy)) {
            return $this->policy_result[$policy->id]->value;
        }
        return null;
    }

    public function getPolicyCacheWithoutNullable(Policy $policy): bool
    {
        if ($this->hasPolicyCache($policy)) {
            return $this->policy_result[$policy->id]->value;
        }
        throw new Error('InvalidOperationException');
    }
}
