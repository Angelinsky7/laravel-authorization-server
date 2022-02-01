<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $resourceType
 * @property Resource $resource
 */
class ResourcePermission extends Permission
{

    protected $table = 'uma_resource_permissions';

    // public function permission(){
    //     return $this->hasOne()
    // }

    // use HasFactory;

    // protected $fillable = ['name', 'description'];

    // public function permissions()
    // {
    //     return $this->belongsToMany(Role::class, 'role_permission');
    // }
}
