<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Support\Facades\Date;

/**
 * @property-read int $id
 * @property string $uri
 */
class Uri extends BaseModel
{
    public function __construct()
    {
        $this->table = config('policy.storage.database.prefix') . 'uris';
    }
}
