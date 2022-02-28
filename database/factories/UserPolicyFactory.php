<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use App\Models\User;
use Darkink\AuthorizationServer\Models\UserPolicy;
use Darkink\AuthorizationServer\Models\Policy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserPolicyFactory extends Factory
{

    protected $model = UserPolicy::class;

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
        return $this->afterMaking(function (UserPolicy $policy) {
            /** @var Policy $parent */
            $parent = Policy::factory()->make();
            $parent->discriminator = get_class($policy);
            $parent->save();
            $policy->id = $parent->id;
        })->afterCreating(function (UserPolicy $policy) {
            $users = User::inRandomOrder()->limit($this->faker->numberBetween(1, 3))->get();
            $policy->users()->saveMany($users);
        });
    }
}
