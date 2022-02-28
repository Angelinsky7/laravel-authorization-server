<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use Darkink\AuthorizationServer\Models\Role;
use Darkink\AuthorizationServer\Models\RolePolicy;
use Darkink\AuthorizationServer\Models\Policy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RolePolicyFactory extends Factory
{

    protected $model = RolePolicy::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [];
    }

    public function configure()
    {
        return $this->afterMaking(function (RolePolicy $policy) {
            /** @var Policy $parent */
            $parent = Policy::factory()->make();
            $parent->discriminator = get_class($policy);
            $parent->save();
            $policy->id = $parent->id;
        })->afterCreating(function (RolePolicy $policy) {
            $role = Role::inRandomOrder()->limit($this->faker->numberBetween(1, 3))->get();
            $policy->roles()->saveMany($role);
        });
    }
}
