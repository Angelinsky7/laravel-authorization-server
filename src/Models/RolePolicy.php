<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\RolePolicyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property Role[] $roles
 */
class RolePolicy extends Policy
{
    use HasFactory;

    protected $table = 'uma_role_policies';
    public $incrementing = false;
    public $timestamps = false;

    public function parent(){
        return $this->morphOne(Policy::class, 'parent', 'discriminator', 'id');
    }

    public function roles(){
        return $this->belongsToMany(Role::class, 'uma_role_policy_role', 'role_policy_id', 'role_id');
    }

    public static function newFactory()
    {
        return RolePolicyFactory::new();
    }

}
