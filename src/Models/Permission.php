<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Traits\HasInheritance;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read int $id
 * @property string $name
 * @property string $description
 * @property DecisionStrategy $decision_strategy
 * @property Policy[] polices
 */
class Permission extends BaseModel
{
    // use HasFactory;
    use HasInheritance;

    protected $table = 'uma_permissions';

    protected $child_classes = [
        ScopePermission::class,
        ResourcePermission::class
    ];

    // protected $fillable = ['name', 'description'];

    // public function permissions()
    // {
    //     return $this->belongsToMany(Role::class, 'role_permission');
    // }
}
