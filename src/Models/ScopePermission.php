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

    // public function newQuery()
    // {
    //     $query = parent::newQuery();
    //     $query = $query
    //         ->join('uma_permissions', 'uma_scope_permissions.id', '=', 'uma_permissions.id')
    //         ->addSelect([
    //             'uma_permissions.name',
    //             'uma_permissions.description',
    //             'uma_permissions.decisionStrategy',
    //             'uma_scope_permissions.*',
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

    // use HasFactory;

    // protected $fillable = ['name', 'description'];

    // public function permissions()
    // {
    //     return $this->belongsToMany(Role::class, 'role_permission');
    // }
}
