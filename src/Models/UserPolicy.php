<?php

namespace Darkink\AuthorizationServer\Models;

use App\Models\User;
use Darkink\AuthorizationServer\Database\Factories\UserPolicyFactory;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property User[] $users
 */
class UserPolicy extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_user_policies';
    public $incrementing = false;
    public $timestamps = false;

    public function parent(){
        return $this->morphOne(Policy::class, 'parent', 'discriminator', 'id');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'uma_user_policy_user', 'user_policy_id', 'user_id');
    }

    public static function newFactory()
    {
        return UserPolicyFactory::new();
    }

    public function evaluate(EvaluatorRequest $request)
    {
        $request->result = $this->users()->where('name', $request->user->name)->count() > 0;
        return $this->parent->evaluate($request);
    }

}
