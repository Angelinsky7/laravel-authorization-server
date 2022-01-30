<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property string $name
 * @property string $displayName
 * @property string $type
 * @property Uri[] $uris
 * @property Scope[] scopes
 * @property string $iconUri
 */
class Resource extends BaseModel
{

    public function uris()
    {
        return $this->belongsToMany(Uri::class, 'resource_uri', 'resource_id', 'uri_id');
    }

    public function scopes()
    {
        return $this->belongsToMany(Scope::class, 'resource_scope', 'resource_id', 'scope_id');
    }

}
