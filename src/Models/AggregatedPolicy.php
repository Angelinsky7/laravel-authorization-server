<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\AggregatedPolicyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

/**
 * @property DecisionStrategy $decision_strategy
 * @property Policy[] $policies
 */
class AggregatedPolicy extends Policy
{
    use HasFactory;

    protected $table = 'uma_aggregated_policies';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'decision_strategy' => DecisionStrategy::class,
    ];

    public function parent(){
        return $this->morphOne(Policy::class, 'parent', 'discriminator', 'id');
    }

    public function policies(){
        return $this->belongsToMany(Policy::class, 'uma_aggregated_policy_policy', 'aggregated_policy_id', 'policy_id');
    }

    public static function newFactory()
    {
        return AggregatedPolicyFactory::new();
    }


}
