<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\Scope;
use Darkink\AuthorizationServer\Models\Uri;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResourceFactory extends Factory
{

    protected $model = Resource::class;

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
            'type' => $this->faker->text(),
            'icon_uri' => $this->faker->url(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Resource $resource) {
            $resource->uris()->saveMany(Uri::factory($this->faker->numberBetween(1,3))->create());
            $resource->scopes()->saveMany(Scope::inRandomOrder()->limit($this->faker->numberBetween(1,3))->get());
        });
    }
}
