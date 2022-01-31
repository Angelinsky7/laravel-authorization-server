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

    public function __construct()
    {
        $this->table = config('policy.storage.database.prefix') . 'scope_permissions';
    }

    // use HasFactory;

    // protected $fillable = ['name', 'description'];

    // public function permissions()
    // {
    //     return $this->belongsToMany(Role::class, 'role_permission');
    // }
}
