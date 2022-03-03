<?php

namespace Darkink\AuthorizationServer\Helpers\Evaluator;

use App\Models\User;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluatorRequestResponseMode;
use Darkink\AuthorizationServer\Models\Client;

class EvaluatorRequest
{

    public Client $client;
    public User $user;
    public array | null $permissions;

    public bool $result = false;

    public EvaluatorResult $evaluator_results;
    public EvaluatorCache $cache;

    /** @var ResouceScopeResult[] $resource_scope_results */
    public array $resource_scope_results = [];

     /** @var PermissionResourceScopeItem[] $permission_resource_scope_items  */
     public array $permission_resource_scope_items  = [];

    /**
     * @param string[] | null $permissions
     */
    public function __construct(Client $client, User $user, array | null $permissions)
    {
        $this->client = $client;
        $this->user = $user;
        $this->permissions = $permissions;

        $this->evaluator_results = new EvaluatorResult();
        $this->cache = new EvaluatorCache();
    }
}
