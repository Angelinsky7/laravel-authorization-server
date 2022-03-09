<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\GroupPolicyFactory;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property Group[] $groups
 */
class GroupPolicy extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_group_policies';
    public $incrementing = false;
    public $timestamps = false;

    public function parent(){
        return $this->morphOne(Policy::class, 'parent', 'discriminator', 'id');
    }

    public function groups(){
        return $this->belongsToMany(Group::class, 'uma_group_policy_group', 'group_policy_id', 'group_id');
    }

    public static function newFactory()
    {
        return GroupPolicyFactory::new();
    }

    public function evaluate(EvaluatorRequest $request)
    {
        $user = $request->user;

        /** @var BelongsToMany $user_groups */
        $user_groups = $user->memberofs();
        $user_group_names = $user_groups->get()->map(fn(Group $p) => $p->name)->toArray();

        /** @var Group[] $filter_groups */
        $filter_groups = $this->groups()->get()->all();

        $result = array_any($filter_groups, fn(Group $p) => array_any($user_group_names, fn(string $a) => $a == $p->name));

        $request->result = $result;
        return $this->parent->evaluate($request);
    }

}
