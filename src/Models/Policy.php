<?php

namespace Darkink\AuthorizationServer\Models;

/**
 * @property-read int $id
 * @property string $name
 * @property string $description
 * @property PolicyLogic logic
 */
class Policy extends BaseModel
{

    protected $table = 'uma_policies';

    protected $child_classes = [
        AggregatePolicy::class,
        ClientPolicy::class
    ];

    protected $fillable = ['name', 'description', 'logic'];

}
