<?php

namespace Darkink\AuthorizationServer\Repositories;

use App\Models\User;
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
            $memberOfsWithoutPrefix = array_map(fn ($p) => substr($p, strlen('g')), $memberOfs);
            $memberOfs = $this->gets()->all()->whereIn(Policy::group()->getKeyName(), $memberOfsWithoutPrefix);
        }

        //TODO(demarco): this is not correct and certainly stupid but it's working for now
        $groups = [];
        $users = [];

        if (count($members) != 0 && !is_object($members[0])) {
            $lookGroups = array_filter($members, fn ($p) => str_starts_with($p, 'g'));
            $lookGroups = array_map(fn ($p) => substr($p, strlen('g')), $lookGroups);
            $groups = $this->gets()->all()->whereIn(Policy::group()->getKeyName(), $lookGroups);

            $lookUsers = array_filter($members, fn ($p) => str_starts_with($p, 'u'));
            $lookUsers = array_map(fn ($p) => substr($p, strlen('g')), $lookUsers);
            $users = Policy::user()->all()->whereIn(Policy::user()->getKeyName(), $lookUsers);
        }

        return [
            'memberOfs' => $memberOfs,
            'members_groups' => $groups,
            'members_users' => $users
        ];
    }

    protected function checkCircualReferencesUp(Group $group, array &$visitedGroups = null)
    {
        if ($visitedGroups == null) {
            $visitedGroups = [];
        }

        if (in_array($group->id, $visitedGroups)) {
            return false;
        }

        $visitedGroups[] = $group->id;

        foreach ($group->memberOfs as $parent) {
            if (!$this->checkCircualReferencesUp($parent, $visitedGroups)) {
                return false;
            }
        }

        return true;
    }

    protected function checkCircualReferencesDown(Group | User $groupOrUser, array &$visitedGroups = null)
    {
        if ($visitedGroups == null) {
            $visitedGroups = [];
        }

        if (in_array($groupOrUser->id, $visitedGroups)) {
            return false;
        }

        $visitedGroups[] = $groupOrUser->id;

        if ($groupOrUser instanceof Group) {
            foreach ($groupOrUser->group_members as $child) {
                if (!$this->checkCircualReferencesDown($child, $visitedGroups)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function checkValidation(Group $group)
    {
        if (!$this->checkCircualReferencesUp($group)) {
            $error = ValidationException::withMessages([
                'memberofs' => ['The Group has a cyclique dependency tree.'],
            ]);
            throw $error;
        }

        if (!$this->checkCircualReferencesDown($group)) {
            $error = ValidationException::withMessages([
                'members' => ['The Group has a cyclique dependency tree.'],
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
