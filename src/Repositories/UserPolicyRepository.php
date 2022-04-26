<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\UserPolicy;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;

class UserPolicyRepository
{
    protected PolicyRepository $policyRepository;

    public function __construct(PolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
    }

    public function find(int $id): UserPolicy
    {
        $policy = Policy::userPolicy();
        return $policy->where($policy->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::userPolicy()->with('parent');
    }

    protected function resolve(mixed $users)
    {
        //TODO(demarco): this is stupid 5 lines later we use only the ids...
        // {
        if (count($users) != 0 && !is_object($users[0])) {
            $users = Policy::user()::all()->whereIn(Policy::user()->getKeyName(), $users);
        }
        // }

        return [
            'users' => $users,
        ];
    }

    public function create(string $name, string $description, PolicyLogic | int $logic, bool $is_system, mixed $permissions, mixed $users): UserPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($users));

            $parent = $this->policyRepository->create($name, $description, $logic, $is_system, $permissions);

            $policy = Policy::userPolicy()->forceFill([
                'id' => $parent->id,
            ]);
            $policy->parent()->save($parent);
            $policy->save();
            $policy->users()->saveMany($users);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function update(UserPolicy $policy, string $name, string $description, PolicyLogic | int $logic, bool $is_system,  mixed $permissions, mixed $users): UserPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($users));

            $this->policyRepository->update($policy->parent, $name, $description, $logic, $is_system, $permissions);
            $policy->save();

            /** @var \Illuminate\Support\Collection $users */
            $policy->users()->sync(is_array($users) ? $users : $users->map(fn ($p) => $p->id)->toArray());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function delete(UserPolicy $policy)
    {
        $this->policyRepository->delete($policy->parent);
    }
}
