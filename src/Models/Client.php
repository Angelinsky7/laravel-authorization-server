<?php

namespace Darkink\AuthorizationServer\Models;

/**
 * @property-read int $id
 * @property bool $enabled
 * @property string $clientId
 * @property string[] $secrets
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
 * missing timestamps
 */
class Client extends BaseModel {

}
