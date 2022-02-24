<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Policy;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Darkink\AuthorizationServer\Policy as AuthorizationServerPolicy;
use Exception;

class PolicyRepository
{
    // protected ResourceRepository $resourceRepository;
    //protected PermissionRepository $permissionRepository;

    // public function __construct()
    // {
    //     $this->permissionRepository = $permissionRepository;
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

    protected function resolve(PolicyLogic | int $logic, mixed $permissions)
    {
        //TODO(demarco): this is stupid 5 lines later we use only the ids...
        // {
        $logic = is_int($logic) ? PolicyLogic::tryFrom($logic) : $logic;

        if (count($permissions) != 0 && !is_object($permissions[0])) {
            $permissions = AuthorizationServerPolicy::policy()::all()->whereIn(AuthorizationServerPolicy::policy()->getKeyName(), $permissions);
        }

        // }

        return [
            'logic' => $logic,
            'permissions' => $permissions
        ];
    }

    public function create(string $name, string $description, PolicyLogic | int $logic, mixed $permissions): Policy
    {
        extract($this->resolve($logic, $permissions));

        $policy = AuthorizationServerPolicy::policy()->forceFill([
            'name' => $name,
            'description' => $description,
            'logic' => $logic->value,
            'discriminator' => 'null'
        ]);

        $policy->save();
        $policy->permissions()->saveMany($permissions);

        return $policy;
    }

    public function update(Policy $policy, string $name, string $description, PolicyLogic | int $logic, mixed $permissions): Policy
    {
        extract($this->resolve($logic, $permissions));

        $policy->forceFill([
            'name' => $name,
            'description' => $description,
            'logic' => $logic->value,
        ]);

        $policy->save();
         /** @var \Illuminate\Support\Collection $permissions */
         $policy->permissions()->sync(is_array($permissions) ? $permissions : $permissions->map(fn ($p) => $p->id)->toArray());

        return $policy;
    }

    public function delete(Policy $policy)
    {
        $policy->delete();
    }
}
