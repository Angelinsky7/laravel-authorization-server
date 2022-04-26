<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\Scope;
use Darkink\AuthorizationServer\Models\ScopePermission;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;

class PermissionRepository
{
    // protected ResourceRepository $resourceRepository;
    // protected ScopeRepository $scopeRepository;

    // public function __construct(
    //     ResourceRepository $resourceRepository,
    //     ScopeRepository $scopeRepository,
    // ) {
    //     $this->resourceRepository = $resourceRepository;
    //     $this->scopeRepository = $scopeRepository;
    // }

    public function find(int $id): Permission
    {
        $permission = Policy::permission();
        return $permission->where($permission->getKeyName(), $id)->first();
    }

    public function gets()
    {
        //TODO(demarco): i think we should use ->get() in this case to ensure the same behavior for each repo
        return Policy::permission()->with('permission');
    }

    protected function resolve(DecisionStrategy | int $decision_strategy, mixed $policies)
    {
        //TODO(demarco): this is stupid 5 lines later we use only the ids...
        // {
        $decision_strategy = is_int($decision_strategy) ? DecisionStrategy::tryFrom($decision_strategy) : $decision_strategy;

        if (count($policies) != 0 && !is_object($policies[0])) {
            $policies = Policy::policy()::all()->whereIn(Policy::policy()->getKeyName(), $policies);
        }

        // }

        return [
            'decision_strategy' => $decision_strategy,
            'policies' => $policies
        ];
    }

    public function create(string $name, string $description, DecisionStrategy | int $decision_strategy, bool $is_system, mixed $policies): Permission
    {
        extract($this->resolve($decision_strategy, $policies));

        $permission = Policy::permission()->forceFill([
            'name' => $name,
            'description' => $description,
            'decision_strategy' => $decision_strategy->value,
            'is_system' => $is_system,
            'discriminator' => 'null'
        ]);
        $permission->save();
        $permission->policies()->saveMany($policies);

        return $permission;
    }

    public function update(Permission $permission, string $name, string $description, DecisionStrategy | int $decision_strategy, bool $is_system, mixed $policies): Permission
    {
        extract($this->resolve($decision_strategy, $policies));

        $permission->forceFill([
            'name' => $name,
            'description' => $description,
            'decision_strategy' => $decision_strategy->value,
            'is_system' => $is_system,
        ]);

        $permission->save();
        /** @var \Illuminate\Support\Collection $policies */
        $permission->policies()->sync(is_array($policies) ? $policies : $policies->map(fn ($p) => $p->id)->toArray());

        return $permission;
    }

    public function delete(Permission $permission)
    {
        $permission->delete();
    }
}
