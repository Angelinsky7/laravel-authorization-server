<?php

namespace Darkink\AuthorizationServer\Http\Requests;

class EvaluatorRun
{
    public EvaluatorRequest $request;
    public EvaluatorResult $result;

    public array $permissionResourceScopeItems;
    public array $resourceScopeResults;

    public function __construct(EvaluatorRequest $request)
    {
        $this->request = $request;
        $this->result = new EvaluatorResult();
    }
}
