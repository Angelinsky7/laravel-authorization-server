<?php

namespace Darkink\AuthorizationServer\Models;

/**
 * @property-read int $id
 * @property string $name
 * @property string $displayName
 * @property string $iconUri
 */
class Scope extends BaseModel
{
    public function __construct()
    {
        $this->table = config('policy.storage.database.prefix') . 'scopes';
    }
}
