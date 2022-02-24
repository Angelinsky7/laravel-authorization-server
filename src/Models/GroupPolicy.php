<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\GroupPolicyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string[] $groups
 */
class GroupPolicy extends Policy
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

}
