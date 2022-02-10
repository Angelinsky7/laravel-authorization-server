<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Permission;
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
        ];
    }

    // public function configure()
    // {
    //     // return $this->afterMaking(function (ScopePermission $permission){
    //     //     $permission->parent()->associate(Permission::factory()->create());
    //     // })->afterCreating(function (ScopePermission $permision) {
    //     //     /** @var Resource $resource */
    //     //     $resource = Resource::inRandomOrder()->limit(1)->first();
    //     //     $permision->resource()->associate($resource);
    //     //     $permision->scopes()->saveMany($resource->scopes()::inRandomOrder()->limit($this->faker->numberBetween(1, 3))->get());
    //     // });
    // }
}
