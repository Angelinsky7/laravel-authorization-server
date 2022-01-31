<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property DecisionStrategy $strategy
 * @property Policy[] $policies
 */
class AggregatePolicy extends Policy
{

    // public function policy(){
    //     return $this->hasOne(Policy::class, 'id');
    // }

    public function policies(){
        return $this->belongsToMany(Policy::class, 'aggregated_policy_policy', 'aggregated_policy_id', 'policy_id');
    }

}
