<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read int $id
 * @property string $name
 * @property string $description
 * @property DecisionStrategy $decisionStrategy
 * @property Policy[] polices
 */
class Permission extends BaseModel
{
    protected $table = 'uma_permissions';

    // use HasFactory;

    // protected $fillable = ['name', 'description'];

    // public function permissions()
    // {
    //     return $this->belongsToMany(Role::class, 'role_permission');
    // }
}
