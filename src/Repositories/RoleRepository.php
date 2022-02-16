<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Role;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleRepository
{
    public function find(int $id): Role
    {
        $role = Policy::role();
        return $role->where($role->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::role();
    }

    protected function resolve(array $parents)
    {
        if (count($parents) != 0 && !is_object($parents[0])) {
            $parents = $this->gets()->all()->whereIn(Policy::role()->getKeyName(), $parents);
        }

        return [
            'parents' => $parents
        ];
    }

    protected function checkCircualReferences(Role $role, array &$visitedRoles = null)
    {
        if ($visitedRoles == null) {
            $visitedRoles = [];
        }

        if (in_array($role->id, $visitedRoles)) {
            return false;
        }

        $visitedRoles[] = $role->id;

        foreach ($role->parents()->get() as $parent) {
            if (!$this->checkCircualReferences($parent, $visitedRoles)) {
                return false;
            }
        }

        return true;
    }

    protected function checkValidation(Role $role)
    {
        if (!$this->checkCircualReferences($role)) {
            $error = ValidationException::withMessages([
                'parents' => ['The role has a cyclique dependency tree.'],
            ]);
            throw $error;
        }
    }

    public function create(string $name, string $display_name, string | null $description, array $parents, bool $system = false): Role
    {
        DB::beginTransaction();

        try {

            extract($this->resolve($parents));

            $role = Policy::role()->forceFill([
                'name' => $name,
                'display_name' => $display_name,
                'description' => $description,
                'system' => $system,
            ]);

            $role->save();

            $role->parents()->saveMany($parents);

            $this->checkValidation($role);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $role;
    }

    public function update(Role $role, string $name, string $display_name, string | null $description, array $parents, bool $system = false): Role
    {
        DB::beginTransaction();

        try {

            extract($this->resolve($parents));

            $role->forceFill([
                'name' => $name,
                'display_name' => $display_name,
                'description' => $description,
                'system' => $system,
            ])->save();

            /** @var \Illuminate\Support\Collection $parents */
            $role->parents()->sync($parents->map(fn ($p) => $p->id)->toArray());

            $this->checkValidation($role);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $role;
    }

    public function delete(Role $role)
    {
        $role->delete();
    }
}
