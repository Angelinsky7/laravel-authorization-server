<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $description
 * @property bool $system
 * @property Role[] $parents
 */
class Role extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_roles';

    // use HasFactory;

    // protected $fillable = ['name', 'description'];

    public function parents(){
        return $this->belongsToMany(Role::class, 'uma_role_role', 'role_id', 'parent_id');
    }

    public function children(){
        return $this->belongsToMany(Role::class, 'uma_role_role', 'parent_id', 'role_id');
    }

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

    protected $searchable = [
        'name',
        'display_name',
        'description'
    ];

    public static function newFactory()
    {
        return RoleFactory::new();
    }
}
