<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string $description
 */
class Permission extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'label', 'description'];

    public function permissions()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}
