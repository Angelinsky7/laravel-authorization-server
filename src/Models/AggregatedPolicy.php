<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\AggregatedPolicyFactory;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property DecisionStrategy $decision_strategy
 * @property Policy[] $policies
 */
class AggregatedPolicy extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_aggregated_policies';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'decision_strategy' => DecisionStrategy::class,
    ];

    public function parent()
    {
        return $this->morphOne(Policy::class, 'parent', 'discriminator', 'id');
    }

    public function policies()
    {
        return $this->belongsToMany(Policy::class, 'uma_aggregated_policy_policy', 'aggregated_policy_id', 'policy_id');
    }

    public static function newFactory()
    {
        return AggregatedPolicyFactory::new();
    }

    public function evaluate(EvaluatorRequest $request)
    {

        /** @var bool | null $result */
        $result = false;

        /** @var int $result_granted */
        $result_granted = 0;
        /** @var int $result_denied */
        $result_denied = 0;

        foreach ($this->policies as $policy) {
            $next_result = $policy->evaluate($request);
            if ($next_result) {
                ++$result_granted;
            } else {
                ++$result_denied;
            }
        }

        switch ($this->decision_strategy) {
            case DecisionStrategy::Affirmative:
                $result = $result_granted > 0;
                break;
            case DecisionStrategy::Consensus:
                $result = ($result_granted - $result_denied) > 0;
                break;
            case DecisionStrategy::Unanimous:
                $result = $result_granted > 0 && $result_denied = 0;
                break;
        }

        $request->result = $result;
        return $this->parent->evaluate($request);
    }
}
