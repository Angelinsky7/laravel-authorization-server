<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Policy;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Darkink\AuthorizationServer\Policy as AuthorizationServerPolicy;
use Exception;

class PolicyRepository
{
    // protected ResourceRepository $resourceRepository;
    // protected PolicyRepository $policyRepository;

    // public function __construct(PolicyRepository $policyRepository)
    // {
    //     $this->policyRepository = $policyRepository;
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

    protected function resolve(PolicyLogic | int $logic)
    {
        //TODO(demarco): this is stupid 5 lines later we use only the ids...
        // {
        $logic = is_int($logic) ? PolicyLogic::tryFrom($logic) : $logic;

        // if (count($permissions) != 0 && !is_object($permissions[0])) {
        //     $permissions = $this->permissionRepository->gets()->all()->whereIn(AuthorizationServerPolicy::policy()->getKeyName(), $scopes);
        // }

        // }

        return [
            'logic' => $logic,
            // 'permissions' => $permissions
        ];
    }

    public function create(string $name, string $description, PolicyLogic | int $logic): Policy
    {
        extract($this->resolve($logic));

        $policy = AuthorizationServerPolicy::policy()->forceFill([
            'name' => $name,
            'description' => $description,
            'logic' => $logic->value,
            'discriminator' => 'null'
        ]);

        $policy->save();

        return $policy;
    }

    public function update(Policy $policy, string $name, string $description, PolicyLogic | int $logic): Policy
    {
        extract($this->resolve($logic));

        $policy->forceFill([
            'name' => $name,
            'description' => $description,
            'logic' => $logic->value,
        ]);
        $policy->save();

        return $policy;
    }

    public function delete(Policy $policy)
    {
        $policy->delete();
    }
}
