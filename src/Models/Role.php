<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $system
 * @property Role[] $parents
 */
class Role extends BaseModel
{
    // use HasFactory;

    // protected $fillable = ['name', 'description'];

    // public function permissions()
    // {
    //     return $this->belongsToMany(Permission::class, 'role_permission');
    // }

    // public function allowTo($permission)
    // {
    //     if (is_string($permission)) {
    //         $permission = Permission::whereName($permission)->firstOrFail();
    //     }
    //     $this->permissions()->save($permission);
    // }

    // public function caption()
    // {
    //     if (isset($this->label)) {
    //         return $this->label;
    //     }
    //     return ucfirst($this->name);
    // }

    // protected $casts = [
    //     'system' => 'boolean',
    // ];

    // protected $searchable = [
    //     'name',
    //     'label',
    //     'description'
    // ];
}
