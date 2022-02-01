<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Policy;

class PermissionRepository
{
    public function find(int $id): Permission
    {
        $permission = Policy::permission();
        return $permission->where($permission->id, $id)->first();
    }

    public function gets()
    {
        return Policy::permission();
    }

    public function create(string $name, string $label, string | null $description, bool $system = false): Permission
    {
        $permission = Policy::permission()->forceFill([
            'name' => $name,
            'label' => $label,
            'description' => $description,
            'system' => $system,
        ]);

        $permission->save();

        return $permission;
    }

    public function update(Permission $permission, string $name, string $label, string | null $description, bool $system = false): Permission
    {
        $permission->forceFill([
            'name' => $name,
            'label' => $label,
            'description' => $description,
            'system' => $system,
        ])->save();

        return $permission;
    }

    public function delete(Permission $permission)
    {
        $permission->delete();
    }
}
