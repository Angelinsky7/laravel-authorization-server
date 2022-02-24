<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read int $id
 * @property string $name
 * @property string $description
 * @property DecisionStrategy $decision_strategy
 * @property Policy[] polices
 * @property ScopePermission | ResourcePermission $permission
 */
class Permission extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_permissions';

    protected $casts = [
        'decision_strategy' => DecisionStrategy::class,
    ];

    public function permission()
    {
        return $this->morphTo('permission', 'discriminator', 'id');
    }

    public function policies(){
        return $this->belongsToMany(Policy::class, 'uma_permission_policy', 'permission_id', 'policy_id');
    }

    // protected $child_classes = [
    //     ScopePermission::class,
    //     ResourcePermission::class
    // ];

    // protected $fillable = ['name', 'description'];

    // public function permissions()
    // {
    //     return $this->belongsToMany(Role::class, 'role_permission');
    // }

    protected $searchable = [
        'name',
        'description',
    ];

    public static function newFactory()
    {
        return PermissionFactory::new();
    }
}
