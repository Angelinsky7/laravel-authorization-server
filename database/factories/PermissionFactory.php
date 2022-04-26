<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Policy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PermissionFactory extends Factory
{

    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'perm_' . $this->faker->unique()->word(),
            'description' => 'Permission ' . $this->faker->word(),
            'decision_strategy' => (DecisionStrategy::cases()[$this->faker->numberBetween(1, 3)])->value,
            'is_system' => false
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Permission $permision) {
            $policies = Policy::inRandomOrder()->limit($this->faker->numberBetween(0, 2))->get();
            if (count($policies) != 0) {
                $permision->polices()->saveMany($policies);
            }
        });
    }
}
