<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use App\Models\User;
use Darkink\AuthorizationServer\Models\AggregatedPolicy;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Policy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AggregatedPolicyFactory extends Factory
{

    protected $model = AggregatedPolicy::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'decision_strategy' => (DecisionStrategy::cases()[$this->faker->numberBetween(1, 3)])->value,
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (AggregatedPolicy $policy) {
            /** @var Policy $parent */
            $parent = Policy::factory()->make();
            $parent->discriminator = get_class($policy);
            $parent->save();
            $policy->id = $parent->id;
        })->afterCreating(function (AggregatedPolicy $policy) {
            $policies = Policy::inRandomOrder()->limit($this->faker->numberBetween(1, 3))->get();
            $policy->policies()->saveMany($policies);
        });
    }
}
