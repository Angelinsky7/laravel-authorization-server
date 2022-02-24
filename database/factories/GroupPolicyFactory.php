<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use Darkink\AuthorizationServer\Models\Group;
use Darkink\AuthorizationServer\Models\GroupPolicy;
use Darkink\AuthorizationServer\Models\Policy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GroupPolicyFactory extends Factory
{

    protected $model = GroupPolicy::class;

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
        return $this->afterMaking(function (GroupPolicy $policy) {
            /** @var Policy $parent */
            $parent = Policy::factory()->make();
            $parent->discriminator = get_class($policy);
            $parent->save();
            $policy->id = $parent->id;
        })->afterCreating(function (GroupPolicy $policy) {
            $group = Group::inRandomOrder()->limit($this->faker->numberBetween(1, 3))->get();
            $policy->groups()->saveMany($group);
        });
    }
}
