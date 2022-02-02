<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\UriFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Date;

/**
 * @property-read int $id
 * @property string $uri
 */
class Uri extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_uris';
    public $timestamps = false;

    public static function newFactory()
    {
        return UriFactory::new();
    }

}
