<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\Scope;
use Darkink\AuthorizationServer\Models\ScopePermission;
use Darkink\AuthorizationServer\Models\Uri;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ScopePermissionFactory extends Factory
{

    protected $model = ScopePermission::class;

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
        return $this->afterMaking(function (ScopePermission $permission) {
            /** @var Permission $parent */
            $parent = Permission::factory()->make();
            $parent->discriminator = get_class($permission);
            $parent->save();
            $permission->id = $parent->id;
            $resource = Resource::inRandomOrder()->limit(1)->first();
            $permission->resource()->associate($resource);
        })->afterCreating(function (ScopePermission $permission) {
            /** @var Resource $resource */
            $resource = $permission->resource;
            $permission->scopes()->saveMany($resource->scopes()->inRandomOrder()->limit($this->faker->numberBetween(1, 3))->get());
        });
    }
}
