<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\ResourcePermissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $resource_type
 * @property Resource $resource
 */
class ResourcePermission extends Permission
{
    use HasFactory;

    protected $table = 'uma_resource_permissions';
    public $incrementing = false;
    public $timestamps = false;

    public function parent()
    {
        return $this->morphOne(Permission::class, 'parent', 'discriminator', 'id');
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resource_id');
    }

    public static function newFactory()
    {
        return ResourcePermissionFactory::new();
    }

}
