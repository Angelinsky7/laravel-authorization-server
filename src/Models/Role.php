<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string $description
 * @property bool $system
 * @property Role $parent
 */
class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'label', 'description'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function allowTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::whereName($permission)->firstOrFail();
        }
        $this->permissions()->save($permission);
    }

    public function caption()
    {
        if (isset($this->label)) {
            return $this->label;
        }
        return ucfirst($this->name);
    }

    protected $casts = [
        'system' => 'boolean',
    ];

}
