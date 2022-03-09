<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use App\Models\User;
use Darkink\AuthorizationServer\Models\Policy;
use Darkink\AuthorizationServer\Models\TimePolicy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TimePolicyFactory extends Factory
{

    protected $model = TimePolicy::class;

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
        return $this->afterMaking(function (TimePolicy $policy) {
            /** @var Policy $parent */
            $parent = Policy::factory()->make();
            $parent->discriminator = get_class($policy);
            $parent->save();
            $policy->id = $parent->id;
        })->afterCreating(function (TimePolicy $policy) {
            // $users = User::inRandomOrder()->limit($this->faker->numberBetween(1, 3))->get();
            // $policy->users()->saveMany($users);
        });
    }
}
