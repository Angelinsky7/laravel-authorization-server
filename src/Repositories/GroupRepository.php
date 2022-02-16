<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Group;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GroupRepository
{
    public function find(int $id): Group
    {
        $group = Policy::group();
        return $group->where($group->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::group();
    }

    protected function resolve(mixed $memberOfs, mixed $members)
    {
        if (count($memberOfs) != 0 && !is_object($memberOfs[0])) {
            $memberOfs = $this->gets()->all()->whereIn(Policy::group()->getKeyName(), $memberOfs);
        }

        //TODO(demarco): this is not correct
        $groups = [];
        $users = [];

        if (count($members) != 0 && !is_object($members[0])) {
            $lookGroups = array_filter($members, fn ($p) => str_starts_with($p, 'g'));
            $groups = $this->gets()->all()->whereIn(Policy::group()->getKeyName(), $lookGroups);
            $lookUsers = array_filter($members, fn ($p) => str_starts_with($p, 'u'));
            $users = Policy::user()->gets()->all()->whereIn(Policy::user()->getKeyName(), $lookUsers);
        }

        return [
            'memberOfs' => $memberOfs,
            'members_groups' => $groups,
            'members_users' => $users
        ];
    }

    protected function checkCircualReferences(Group $group, array &$visitedGroups = null)
    {
        //TODO(demarco): Please implement correctly

        // if ($visitedGroups == null) {
        //     $visitedGroups = [];
        // }

        // if (in_array($group->id, $visitedGroups)) {
        //     return false;
        // }

        // $visitedGroups[] = $group->id;

        // foreach ($group->parents()->get() as $parent) {
        //     if (!$this->checkCircualReferences($parent, $visitedGroups)) {
        //         return false;
        //     }
        // }

        return true;
    }

    protected function checkValidation(Group $group)
    {
        if (!$this->checkCircualReferences($group)) {
            $error = ValidationException::withMessages([
                'parents' => ['The Group has a cyclique dependency tree.'],
            ]);
            throw $error;
        }
    }

    public function create(string $name, string $display_name, string | null $description, array $memberOfs, array $members, bool $system = false): Group
    {
        DB::beginTransaction();

        try {

            extract($this->resolve($memberOfs, $members));

            $group = Policy::group()->forceFill([
                'name' => $name,
                'display_name' => $display_name,
                'description' => $description,
                'system' => $system,
            ]);

            $group->save();

            $group->memberOfs()->saveMany($memberOfs);
            $group->group_members()->saveMany($members_groups);
            $group->user_members()->saveMany($members_users);

            $this->checkValidation($group);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $group;
    }

    public function update(Group $group, string $name, string $display_name, string | null $description, array $memberOfs, array $members, bool $system = false): Group
    {
        DB::beginTransaction();

        try {

            extract($this->resolve($memberOfs, $members));

            $group->forceFill([
                'name' => $name,
                'display_name' => $display_name,
                'description' => $description,
                'system' => $system,
            ])->save();

            /** @var \Illuminate\Support\Collection $memberOfs */
            $group->memberOfs()->sync(is_array($memberOfs) ? $memberOfs : $memberOfs->map(fn ($p) => $p->id)->toArray());
            /** @var \Illuminate\Support\Collection $members_groups */
            $group->group_members()->sync(is_array($members_groups) ? $members_groups : $members_groups->map(fn ($p) => $p->id)->toArray());
            /** @var \Illuminate\Support\Collection $members_users */
            $group->user_members()->sync(is_array($members_users) ? $members_users : $members_users->map(fn ($p) => $p->id)->toArray());

            $this->checkValidation($group);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $group;
    }

    public function delete(Group $group)
    {
        $group->delete();
    }
}
