<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use App\Models\User;
use Darkink\AuthorizationServer\Models\Client;
use Darkink\AuthorizationServer\Models\ClientPolicy;
use Darkink\AuthorizationServer\Models\Policy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClientPolicyFactory extends Factory
{

    protected $model = ClientPolicy::class;

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
        return $this->afterMaking(function (ClientPolicy $policy) {
            /** @var Policy $parent */
            $parent = Policy::factory()->make();
            $parent->discriminator = get_class($policy);
            $parent->save();
            $policy->id = $parent->id;
        })->afterCreating(function (ClientPolicy $policy) {
            $clients = Client::inRandomOrder()->limit($this->faker->numberBetween(1, 3))->get();
            $policy->clients()->saveMany($clients);
        });
    }
}
