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
    public function __construct()
    {
        $this->table = config('policy.storage.database.prefix') . 'policies';
    }
}
