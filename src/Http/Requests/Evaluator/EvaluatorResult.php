<?php

namespace Darkink\AuthorizationServer\Http\Requests\Evaluator;

class EvaluatorResult
{
    public EvaluatorRequest $request;

    /** @var PermissionResourceScopeItem[] $permissionResourceScopeItems */
    public array $permissionResourceScopeItems = [];
    /** @var ResourceScopeResult[] $resourceScopeResults */
    public array $resourceScopeResults = [];

    public function __construct(EvaluatorRequest $request)
    {
        $this->request = $request;
    }
}
