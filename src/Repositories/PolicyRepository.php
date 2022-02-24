<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Policy;
use Darkink\AuthorizationServer\Policy as AuthorizationServerPolicy;
use Exception;

class PolicyRepository
{
    // protected ResourceRepository $resourceRepository;
    // protected ScopeRepository $scopeRepository;

    // public function __construct(ResourceRepository $resourceRepository, ScopeRepository $scopeRepository)
    // {
    //     $this->resourceRepository = $resourceRepository;
    //     $this->scopeRepository = $scopeRepository;
    // }

    public function find(int $id): Policy
    {
        $policy = AuthorizationServerPolicy::policy();
        return $policy->where($policy->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return AuthorizationServerPolicy::policy()->with('policy');
    }

    public function create(string $name): Policy
    {
        $policy = Policy::permission()->forceFill([
            // 'name' => $name,
            // 'description' => $description,
            // 'decision_strategy' => $decision_strategy->value,
            // 'discriminator' => 'null'
        ]);
        $policy->save();

        return $policy;
    }

    public function update(Policy $policy, string $name): Policy
    {
        $policy->forceFill([
            // 'name' => $name,
            // 'description' => $description,
            // 'decision_strategy' => $decision_strategy->value,
        ]);
        $policy->save();

        return $policy;
    }

    public function delete(Policy $policy)
    {
        $policy->delete();
    }
}
