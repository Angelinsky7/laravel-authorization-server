<?php

namespace Darkink\AuthorizationServer\Models;

use App\Models\Client as ModelsClient;
use Illuminate\Support\Facades\Date;

/**
 * @property-read int $id
 * @property bool $enabled
//  * @property string $client_id
 * @property Secret[] $secrets
 * @property bool $require_client_secret
 * @property string $client_name
 * @property string $description
 * @property string $client_uri
 * @property PolicyEnforcement $policy_enforcement
 * @property DecisionStrategy $decision_strategy
 * @property bool $analyse_mode_enabled
 * @property bool $json_mode_enabled
 * @property string $permission_splitter
 * @property bool $all_resources
 * @property Resource[] $resources
 * @property bool $all_scopes
 * @property Scope[] $scopes
 * @property bool $all_roles
 * @property Role[] $roles
 * @property bool $all_groups
 * @property Group[] $groups
 * @property bool $all_policies
 * @property Policy[] $policies
 * @property bool $all_permissions
 * @property Permission[] $permissions
 * @property ModelsClient $oauth
 * @property-read Date $created_at
 * @property-read Date $updated_at
 */
class Client extends BaseModel
{

    protected $table = 'uma_clients';

    protected $casts = [
        'policy_enforcement' => PolicyEnforcement::class,
        'decision_strategy' => DecisionStrategy::class,
        'enabled' => 'boolean',
        'require_client_secret' => 'boolean',
        'analyse_mode_enabled' => 'boolean',
        'json_mode_enabled' => 'boolean',
        'all_resources' => 'boolean',
        'all_scopes' => 'boolean',
        // 'all_roles' => 'boolean',
        // 'all_groups' => 'boolean',
        // 'all_policies' => 'boolean',
        'all_permissions' => 'boolean',
    ];

    protected $attributes = [
        'policy_enforcement' => PolicyEnforcement::Enforcing,
        'decision_strategy' => DecisionStrategy::Affirmative,
        'permission_splitter' => '#',
        'all_resources' => true,
        'all_scopes' => true,
        // 'all_roles' => true,
        // 'all_groups' => true,
        // 'all_policies' => true,
        'all_permissions' => true,
    ];

    protected $hidden = [
        'secret',
        'secrets',
    ];

    public function secrets()
    {
        return $this->belongsToMany(Secret::class, 'uma_client_secret', 'client_id', 'secret_id');
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'uma_client_resource', 'client_id', 'resource_id');
    }

    public function scopes()
    {
        return $this->belongsToMany(Scope::class, 'uma_client_scope', 'client_id', 'scope_id');
    }

    public function policies()
    {
        return $this->belongsToMany(Policy::class, 'uma_client_policy', 'client_id', 'policy_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'uma_client_permission', 'client_id', 'permission_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'uma_client_role', 'client_id', 'role_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'uma_client_group', 'client_id', 'group_id');
    }

    public function oauth()
    {
        return $this->belongsTo(ModelsClient::class, 'oauth_id');
    }
}
