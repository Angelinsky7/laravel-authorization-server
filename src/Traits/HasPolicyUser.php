<?php

namespace Darkink\AuthorizationServer\Traits;

use Darkink\AuthorizationServer\Models\Group;
use Darkink\AuthorizationServer\Models\Role;

trait HasPolicyUser
{
    public function memberofs()
    {
        return $this->belongsToMany(Group::class, 'uma_group_member', 'member_user_id', 'group_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'uma_user_role', 'user_id', 'role_id');
    }

}
