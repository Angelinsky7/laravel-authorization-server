<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read int $id
 * @property string $name
 * @property string $description
 * @property DecisionStrategy strategy
 * @property Policy[] polices
 */
class Permission extends BaseModel
{
    // use HasFactory;

    // protected $fillable = ['name', 'description'];

    // public function permissions()
    // {
    //     return $this->belongsToMany(Role::class, 'role_permission');
    // }
}
