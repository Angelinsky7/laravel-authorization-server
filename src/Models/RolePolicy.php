<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\RolePolicyFactory;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property Policy $parent;
 * @property Role[] $roles
 */
class RolePolicy extends BaseModel
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

    public function evaluate(EvaluatorRequest $request)
    {
        $user = $request->user;

        /** @var BelongsToMany $user_roles */
        $user_roles = $user->roles();
        $user_role_names = $user_roles->get()->map(fn(Role $p) => $p->name)->toArray();

        /** @var role[] $filter_roles */
        $filter_roles = $this->roles()->get()->all();

        $result = array_any($filter_roles, fn(role $p) => array_any($user_role_names, fn(string $a) => $a == $p->name));

        $request->result = $result;
        return $this->parent->evaluate($request);
    }

}
