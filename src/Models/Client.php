<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Support\Facades\Date;

/**
 * @property-read int $id
 * @property bool $enabled
 * @property string $clientId
 * @property Secret[] $secrets
 * @property bool $requireClientSecret
 * @property string $clientName
 * @property string $description
 * @property string $clientUri
 * @property PolicyEnforcement $policyEnforcement
 * @property DecisionStrategy $decisionStrategy
 * @property bool $analyseModeEnabled
 * @property string $permissionSplitter (?)
 * @property Resource[] $resources
 * @property Scope[] $scopes
 * @property Role[] $roles
 * @property Policy[] $policies
 * @property Permission[] $permissions
 * @property-read Date $created_at
 * @property-read Date $updated_at
 */
class Client extends BaseModel {

    protected $table = 'uma_clients';

    public function secrets() {
        return $this->belongsToMany(Secret::class, 'client_secret', 'client_id', 'secret_id');
    }

    public function resources() {
        return $this->belongsToMany(Resource::class, 'client_resource', 'client_id', 'resource_id');
    }

    public function scopes() {
        return $this->belongsToMany(Scope::class, 'client_scope', 'client_id', 'scope_id');
    }

    public function policies() {
        return $this->belongsToMany(Policy::class, 'client_policy', 'client_id', 'policy_id');
    }

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'client_permission', 'client_id', 'permission_id');
    }

}
