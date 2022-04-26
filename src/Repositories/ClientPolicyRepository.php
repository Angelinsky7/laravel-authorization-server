<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\ClientPolicy;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;

class ClientPolicyRepository
{
    protected PolicyRepository $policyRepository;

    public function __construct(PolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
    }

    public function find(int $id): ClientPolicy
    {
        $policy = Policy::clientPolicy();
        return $policy->where($policy->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::clientPolicy()->with('parent');
    }

    protected function resolve(mixed $clients)
    {
        //TODO(demarco): this is stupid 5 lines later we use only the ids...
        // {
        if (count($clients) != 0 && !is_object($clients[0])) {
            $clients = Policy::client()::all()->whereIn(Policy::client()->getKeyName(), $clients);
        }
        // }

        return [
            'clients' => $clients,
        ];
    }

    public function create(string $name, string $description, PolicyLogic | int $logic, bool $is_system, mixed $permissions, mixed $clients): ClientPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($clients));

            $parent = $this->policyRepository->create($name, $description, $logic, $is_system, $permissions);

            $policy = Policy::ClientPolicy()->forceFill([
                'id' => $parent->id,
            ]);
            $policy->parent()->save($parent);
            $policy->save();
            $policy->clients()->saveMany($clients);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function update(ClientPolicy $policy, string $name, string $description, PolicyLogic | int $logic, bool $is_system, mixed $permissions, mixed $clients): ClientPolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            extract($this->resolve($clients));

            $this->policyRepository->update($policy->parent, $name, $description, $logic, $is_system, $permissions);
            $policy->save();

            /** @var \Illuminate\Support\Collection $clients */
            $policy->clients()->sync(is_array($clients) ? $clients : $clients->map(fn ($p) => $p->id)->toArray());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function delete(ClientPolicy $policy)
    {
        $this->policyRepository->delete($policy->parent);
    }
}
