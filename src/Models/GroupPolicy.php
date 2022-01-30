<?php

namespace Darkink\AuthorizationServer\Models;

/**
 * @property string[] $groups
 */
class GroupPolicy extends Policy
{

    public function groups(){
        return $this->belongsToMany(GroupPolicyGroup::class, 'group_policy_group', 'group_policy_id', 'group_id');
    }

}
