<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use Darkink\AuthorizationServer\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{

    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'role_' . $this->faker->unique()->word(),
            'display_name' => 'Role ' . $this->faker->word(),
            'description' => $this->faker->text(),
        ];
    }
}
