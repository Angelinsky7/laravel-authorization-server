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

    public function newModelQuery()
    {
        $result = parent::newModelQuery();
        Log::debug($result->toSql());
        return $result;
    }

    protected function performInsert(Builder $query){
        Log::debug($query->toSql());
        parent::performInsert($query);
    }

    protected function performUpdate(Builder $query){
        Log::debug($query->toSql());
        parent::performUpdate($query);
    }

    protected function getAttributesForInsert(){
        $result = parent::getAttributesForInsert();
        Log::debug($result);
        return $result;
    }

    protected function insertAndSetId(Builder $query, $attributes){
        Log::debug($attributes);
        Log::debug($query->toSql());

        $id = $query->insertGetId($attributes, $keyName = $this->getKeyName());

        $parent = new Policy();

        $this->setAttribute($keyName, $id);
    }


    // protected static function booted()
    // {
    //     static::addGlobalScope('parent', function (Builder $builder) {
    //         $builder
    //             ->join('uma_policies', 'uma_aggregated_policies.id', '=', 'uma_policies.id')
    //             ->addSelect(['policies.name', 'policies.description', 'policies.logic']);
    //     });
    // }

}
