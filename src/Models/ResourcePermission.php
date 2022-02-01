<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Traits\HasParent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $resource_type
 * @property Resource $resource
 */
class ResourcePermission extends Permission
{
    use HasParent;

    protected $table = 'uma_resource_permissions';

    public function resource(){
        return $this->belongsTo(Resource::class, 'resource_id');
    }

}
