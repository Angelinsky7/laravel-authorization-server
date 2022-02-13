<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\ResourcePermission;
use Darkink\AuthorizationServer\Models\Uri;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResourcePermissionFactory extends Factory
{

    protected $model = ResourcePermission::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'resource_type' => $this->faker->numberBetween(0, 1) ? $this->faker->slug(3) : null
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (ResourcePermission $permission) {
            /** @var Permission $parent */
            $parent = Permission::factory()->make();
            $parent->discriminator = get_class($permission);
            $parent->save();
            $permission->id = $parent->id;
            if ($permission->resource_type == null || $this->faker->numberBetween(0, 1)) {
                $resource = Resource::inRandomOrder()->limit(1)->first();
                $permission->resource()->associate($resource);
            }
        })->afterCreating(function (ResourcePermission $permission) {
        });
    }
}
