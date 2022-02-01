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

    protected $child_classes = [
        AggregatePolicy::class,
        ClientPolicy::class
    ];

    protected $fillable = ['name', 'description', 'logic'];

    public function __construct()
    {
        $this->table = config('policy.storage.database.prefix') . 'policies';
    }

}
