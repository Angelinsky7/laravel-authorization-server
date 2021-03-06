<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\ScopeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read int $id
 * @property string $name
 * @property string $display_name
 * @property string $icon_uri
 */
class Scope extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_scopes';

    public static function newFactory()
    {
        return ScopeFactory::new();
    }

    protected $searchable = [
        'name',
        'display_name',
        'icon_uri'
    ];

}
