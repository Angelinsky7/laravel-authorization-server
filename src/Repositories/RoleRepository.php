<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Role;
use Darkink\AuthorizationServer\Policy;

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

    public function create(string $name, string $display_name, string | null $description, bool $system = false): Role
    {
        $role = Policy::role()->forceFill([
            'name' => $name,
            'display_name' => $display_name,
            'description' => $description,
            'system' => $system,
        ]);

        $role->save();

        return $role;
    }

    public function update(Role $role, string $name, string $display_name, string | null $description, bool $system = false): Role
    {
        $role->forceFill([
            'name' => $name,
            'display_name' => $display_name,
            'description' => $description,
            'system' => $system,
        ])->save();

        return $role;
    }

    public function delete(Role $role)
    {
        $role->delete();
    }
}
