<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\SecretFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Date;

/**
 * @property-read int $id
 * @property string $description
 * @property string $value
 * @property Date $expiration
 * @property-read Date $create_at
 * @property-read Date $updated_at
 */
class Secret extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_secrets';

    public static function newFactory()
    {
        return SecretFactory::new();
    }

}
