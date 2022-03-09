<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\TimePolicy;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Darkink\AuthorizationServer\Models\TimeRange;
use Darkink\AuthorizationServer\Policy;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;

class TimePolicyRepository
{
    protected PolicyRepository $policyRepository;

    public function __construct(PolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
    }

    public function find(int $id): TimePolicy
    {
        $policy = Policy::timePolicy();
        return $policy->where($policy->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::timePolicy()->with('parent');
    }

    // protected function resolve(mixed $users)
    // {
    //     //TODO(demarco): this is stupid 5 lines later we use only the ids...
    //     // {
    //     if (count($users) != 0 && !is_object($users[0])) {
    //         $users = Policy::user()::all()->whereIn(Policy::user()->getKeyName(), $users);
    //     }
    //     // }

    //     return [
    //         'users' => $users,
    //     ];
    // }

    public function create(string $name, string $description, PolicyLogic | int $logic, mixed $permissions, DateTime $not_before, DateTime $not_after, TimeRange $day_of_month, TimeRange $month, TimeRange $year, TimeRange $hour, TimeRange $minute): TimePolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            // extract($this->resolve($users));

            $parent = $this->policyRepository->create($name, $description, $logic, $permissions);

            $policy = Policy::timePolicy()->forceFill([
                'id' => $parent->id,
                'not_before' => $not_before,
                'not_after' => $not_after
            ]);
            $policy->parent()->save($parent);
            $policy->day_of_month()->associate($day_of_month);
            $policy->month()->associate($month);
            $policy->year()->associate($year);
            $policy->hour()->associate($hour);
            $policy->minute()->associate($minute);
            $policy->save();
            // $policy->users()->saveMany($users);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function update(TimePolicy $policy, string $name, string $description, PolicyLogic | int $logic, mixed $permissions, DateTime $not_before, DateTime $not_after, TimeRange $day_of_month, TimeRange $month, TimeRange $year, TimeRange $hour, TimeRange $minute): TimePolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            // extract($this->resolve($users));

            $this->policyRepository->update($policy->parent, $name, $description, $logic, $permissions);

            $policy->forceFill([
                'not_before' => $not_before,
                'not_after' => $not_after
            ]);
            $policy->month()->associate($month);
            $policy->year()->associate($year);
            $policy->hour()->associate($hour);
            $policy->minute()->associate($minute);
            $policy->save();

            /** @var \Illuminate\Support\Collection $users */
            // $policy->users()->sync(is_array($users) ? $users : $users->map(fn ($p) => $p->id)->toArray());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function delete(TimePolicy $policy)
    {
        $this->policyRepository->delete($policy->parent);
    }
}
