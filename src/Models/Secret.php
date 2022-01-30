<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Support\Facades\Date;

/**
 * @property-read int $id
 * @property string $description
 * @property string $value
 * @property Date $expiration
 * @property-read Date $create_at
 * @property-read Date $updated_at
 */
class Secret extends BaseModel {

}
