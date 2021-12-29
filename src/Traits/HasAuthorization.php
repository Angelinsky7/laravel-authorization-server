<?php

namespace Darkink\AuthorizationServer\Traits;

use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Role;
use Error;

trait HasAuthorization
{

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function assignRole(Role | string $role)
    {
        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrFail();
        }
        $this->roles()->save($role);
    }

    public function permissions()
    {
        return $this->roles->map->permissions->flatten()->pluck('name')->unique();
    }

    public function hasRole(Role | string $role): bool
    {
        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrFail();
        }
        return $this->roles->contains($role);
    }

    public function hasPermission(Permission | string $permission): bool
    {
        if (is_string($permission)) {
            $permission = Permission::whereName($permission)->firstOrFail();
        }
        return $this->permissions()->contains($permission);
    }
}
