<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Traits\HasParent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $resourceId
 * @property Resource $resource
 * @property Scope[] $scopes
 */
class ScopePermission extends Permission
{
    use HasParent;

    protected $table = 'uma_scope_permissions';

    public function resource(){
        return $this->belongsTo(Resource::class, 'resource_id');
    }

    public function scopes(){
        return $this->belongsToMany(Scope::class, 'uma_scope_permission_scope', 'scope_permission_id', 'scope_id');
    }

}
