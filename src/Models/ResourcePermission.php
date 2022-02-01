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

    // public function newQuery()
    // {
    //     $query = parent::newQuery();
    //     $query = $query
    //         ->join('uma_permissions', 'uma_resource_permissions.id', '=', 'uma_permissions.id')
    //         ->addSelect([
    //             'uma_permissions.name',
    //             'uma_permissions.description',
    //             'uma_permissions.decision_strategy',
    //             'uma_resource_permissions.*',
    //         ]);

    //     return $query;
    // }

    // protected function insertAndSetId(Builder $query, $attributes)
    // {
    //     $attributes = $this->inheritanceInsertAndSetId($query, $attributes);
    //     parent::insertAndSetId($query, $attributes);
    // }

    // protected function performUpdate(Builder $query)
    // {
    //     $this->inheritancePerformUpdate($query);
    // }

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
