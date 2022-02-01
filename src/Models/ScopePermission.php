<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $resourceId
 * @property Resource $resource
 * @property Scope[] $scopes
 */
class ScopePermission extends Permission
{

    protected $table = 'uma_scope_permissions';

    // use HasFactory;

    // protected $fillable = ['name', 'description'];

    // public function permissions()
    // {
    //     return $this->belongsToMany(Role::class, 'role_permission');
    // }
}
