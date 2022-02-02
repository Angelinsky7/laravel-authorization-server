<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use Darkink\AuthorizationServer\Models\Scope;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScopeFactory extends Factory
{

    protected $model = Scope::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'display_name' => $this->faker->text(),
            'icon_uri' => $this->faker->url(),
        ];
    }
}
