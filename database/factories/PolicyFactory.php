<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use Darkink\AuthorizationServer\Models\Policy;
use Darkink\AuthorizationServer\Models\PolicyLogic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PolicyFactory extends Factory
{

    protected $model = Policy::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'policy_' . $this->faker->unique()->word(),
            'description' => 'Policy ' . $this->faker->word(),
            'logic' => (PolicyLogic::cases()[$this->faker->numberBetween(1, 2)])->value,
            'is_system' => false
        ];
    }
}
