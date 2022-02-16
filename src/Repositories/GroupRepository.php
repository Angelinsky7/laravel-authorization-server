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

    protected function resolve(array $memberOfs, array $members)
    {
        // if (count($parents) != 0 && !is_object($parents[0])) {
        //     $parents = $this->gets()->all()->whereIn(Policy::group()->getKeyName(), $parents);
        // }

        return [
            'memberOfs' => $memberOfs,
            'members' => $members
        ];
    }

    protected function checkCircualReferences(Group $group, array &$visitedGroups = null)
    {
        if ($visitedGroups == null) {
            $visitedGroups = [];
        }

        if (in_array($group->id, $visitedGroups)) {
            return false;
        }

        $visitedGroups[] = $group->id;

        foreach ($group->parents()->get() as $parent) {
            if (!$this->checkCircualReferences($parent, $visitedGroups)) {
                return false;
            }
        }

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
            $group->members()->saveMany($members);

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
            $group->memberOfs()->sync($memberOfs->map(fn ($p) => $p->id)->toArray());
            /** @var \Illuminate\Support\Collection $members */
            $group->members()->sync($members->map(fn ($p) => $p->id)->toArray());

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
