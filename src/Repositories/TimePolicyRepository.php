<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\TimePolicy;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Darkink\AuthorizationServer\Models\TimeRange;
use Darkink\AuthorizationServer\Policy;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    protected function saveOrUpdateTimerange(TimeRange | null $timerange, TimeRange | null $policyModel, BelongsTo $policyRelation, TimePolicy $timePolicy)
    {
        if ($timerange != null && ($timerange->from != null && $timerange->to != null)) {
            if ($policyModel != null) {
                $policyModel->forceFill([
                    'from' => $timerange->from,
                    'to' => $timerange->to
                ]);
                $policyModel->save();
            } else {
                $timerange->save();
                $policyRelation->associate($timerange);
            }
        } else if ($policyModel != null) {
            $policyRelation->dissociate($policyModel);
            $timePolicy->save();
            $policyModel->delete();
        }
    }

    public function create(string $name, string $description, PolicyLogic | int $logic, bool $is_system, mixed $permissions, DateTime | null $not_before, DateTime | null $not_after, TimeRange | null $day_of_month, TimeRange | null $month, TimeRange | null $year, TimeRange | null $hour, TimeRange | null $minute): TimePolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            // extract($this->resolve($users));

            $parent = $this->policyRepository->create($name, $description, $logic, $is_system, $permissions);

            $policy = Policy::timePolicy()->forceFill([
                'id' => $parent->id,
                'not_before' => $not_before,
                'not_after' => $not_after
            ]);
            $policy->parent()->save($parent);

            $this->saveOrUpdateTimerange($day_of_month, $policy->day_of_month, $policy->day_of_month(), $policy);
            $this->saveOrUpdateTimerange($month, $policy->month, $policy->month(), $policy);
            $this->saveOrUpdateTimerange($year, $policy->year, $policy->year(), $policy);
            $this->saveOrUpdateTimerange($hour, $policy->hour, $policy->hour(), $policy);
            $this->saveOrUpdateTimerange($minute, $policy->minute, $policy->minute(), $policy);

            $policy->save();
            // $policy->users()->saveMany($users);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $policy;
    }

    public function update(TimePolicy $policy, string $name, string $description, PolicyLogic | int $logic, bool $is_system, mixed $permissions, DateTime | null $not_before, DateTime | null $not_after, TimeRange | null $day_of_month, TimeRange | null $month, TimeRange | null $year, TimeRange | null $hour, TimeRange | null $minute): TimePolicy
    {
        DB::beginTransaction();

        try {

            // //TODO(demarco): this is stupid 5 lines later we use only the ids...
            // extract($this->resolve($users));

            $this->policyRepository->update($policy->parent, $name, $description, $logic, $is_system, $permissions);

            $policy->forceFill([
                'not_before' => $not_before,
                'not_after' => $not_after
            ]);

            $this->saveOrUpdateTimerange($day_of_month, $policy->day_of_month, $policy->day_of_month(), $policy);
            $this->saveOrUpdateTimerange($month, $policy->month, $policy->month(), $policy);
            $this->saveOrUpdateTimerange($year, $policy->year, $policy->year(), $policy);
            $this->saveOrUpdateTimerange($hour, $policy->hour, $policy->hour(), $policy);
            $this->saveOrUpdateTimerange($minute, $policy->minute, $policy->minute(), $policy);

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
        DB::beginTransaction();

        try {
            $this->policyRepository->delete($policy->parent);

            if ($policy->day_of_month != null) {
                $policy->day_of_month->delete();
            }
            if ($policy->month != null) {
                $policy->month->delete();
            }
            if ($policy->year != null) {
                $policy->year->delete();
            }
            if ($policy->hour != null) {
                $policy->hour->delete();
            }
            if ($policy->minute != null) {
                $policy->minute->delete();
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();
    }
}
