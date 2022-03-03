<?php

namespace Darkink\AuthorizationServer\Models;

use App\Models\Client as ModelsClient;
use Illuminate\Support\Facades\Date;

/**
 * @property-read int $id
 * @property bool $enabled
 * @property string $client_id
 * @property Secret[] $secrets
 * @property bool $require_client_secret
 * @property string $client_name
 * @property string $description
 * @property string $client_uri
 * @property PolicyEnforcement $policy_enforcement
 * @property DecisionStrategy $decision_strategy
 * @property bool $analyse_mode_enabled
 * @property string $permission_splitter
 * @property Resource[] $resources
 * @property Scope[] $scopes
 * @property Role[] $roles
 * @property Policy[] $policies
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
    ];

    protected $attributes = [
        'policy_enforcement' => PolicyEnforcement::Enforcing,
        'decision_strategy' => DecisionStrategy::Affirmative,
        'permission_splitter' => '#'
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

    public function oauth()
    {
        return $this->belongsTo(ModelsClient::class, 'oauth_id');
    }
}
