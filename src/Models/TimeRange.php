<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Support\Facades\Date;

/**
 * @property-read int $id
 * @property int $from
 * @property int $to
 */
class TimeRange extends BaseModel
{
    public function __construct()
    {
        $this->table = config('policy.storage.database.prefix') . 'timeranges';
    }
}
