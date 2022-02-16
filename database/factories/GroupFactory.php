<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use Darkink\AuthorizationServer\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{

    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'group_' . $this->faker->unique()->word(),
            'display_name' => 'Group ' . $this->faker->word(),
            'description' => $this->faker->text(),
        ];
    }
}
