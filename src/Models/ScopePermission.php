<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\ScopePermissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $resourceId
 * @property Resource $resource
 * @property Scope[] $scopes
 * @property Permission $parent
 */
class ScopePermission extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_scope_permissions';
    public $incrementing = false;
    public $timestamps = false;

    public function parent(){
        return $this->morphOne(Permission::class, 'parent', 'discriminator', 'id');
    }

    public function resource(){
        return $this->belongsTo(Resource::class, 'resource_id');
    }

    public function scopes(){
        return $this->belongsToMany(Scope::class, 'uma_scope_permission_scope', 'scope_permission_id', 'scope_id');
    }

    public static function newFactory()
    {
        return ScopePermissionFactory::new();
    }

}
