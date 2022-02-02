<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\ResourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property string $name
 * @property string $display-name
 * @property string $type
 * @property Uri[] $uris
 * @property Scope[] scopes
 * @property string $icon-uri
 */
class Resource extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_resources';

    public function uris()
    {
        return $this->belongsToMany(Uri::class, 'uma_resource_uri', 'resource_id', 'uri_id');
    }

    public function scopes()
    {
        return $this->belongsToMany(Scope::class, 'uma_resource_scope', 'resource_id', 'scope_id');
    }

    public static function newFactory()
    {
        return ResourceFactory::new();
    }

}
