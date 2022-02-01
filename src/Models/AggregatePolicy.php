<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

/**
 * @property DecisionStrategy $strategy
 * @property Policy[] $policies
 */
class AggregatePolicy extends Policy
{

    protected $table = 'uma_aggregated_policies';
    protected $fillable = ['name', 'description', 'logic'];

    // public function policy(){
    //     return $this->hasOne(Policy::class, 'id');
    // }

    public function policies(){
        return $this->belongsToMany(Policy::class, 'uma_aggregated_policy_policy', 'aggregated_policy_id', 'policy_id');
    }

    public function newQuery(){
        $query = parent::newQuery();
        $query = $query
            ->join('uma_policies', 'uma_aggregated_policies.id', '=', 'uma_policies.id')
            ->addSelect([
                'uma_policies.name',
                'uma_policies.description',
                'uma_policies.logic',
                'uma_aggregated_policies.*',
            ]);

        return $query;
    }

    protected function insertAndSetId(Builder $query, $attributes){
        $attributes = $this->inheritanceInsertAndSetId($query, $attributes);
        parent::insertAndSetId($query, $attributes);
    }

    protected function performUpdate(Builder $query)
    {
        $this->inheritancePerformUpdate($query);
    }

}
