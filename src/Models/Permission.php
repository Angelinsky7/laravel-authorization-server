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

    public function __construct()
    {
        $this->table = config('policy.storage.database.prefix') . 'permissions';
    }

    // use HasFactory;

    // protected $fillable = ['name', 'description'];

    // public function permissions()
    // {
    //     return $this->belongsToMany(Role::class, 'role_permission');
    // }
}
