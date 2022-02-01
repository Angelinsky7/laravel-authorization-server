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

    protected $table = 'uma_timeranges';

}
