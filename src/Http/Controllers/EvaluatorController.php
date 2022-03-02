<?php

namespace Darkink\AuthorizationServer\Http\Controllers;

use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest as EvaluatorEvaluatorRequest;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluatorRequest;
use Darkink\AuthorizationServer\Services\IEvaluatorService;

class EvaluatorController
{

    protected IEvaluatorService $evaluator;

    public function __construct(IEvaluatorService $evaluator)
    {
        $this->evluator = $evaluator;
    }

    public function process(EvaluatorRequest $request)
    {
        $validated = $request->validated();

        // $client = await _clientStore.GetFromClientIdAsync(permissionRequest.ClientId);

        $evaluatorRequest = new EvaluatorEvaluatorRequest($client, $permissionRequest->user, $permissionRequest->permissions);
        $this->evaluator->evaluate($evaluatorRequest);
        $evaulation = $this->evaluator->buildEvaluation($evaluatorRequest);

        switch ($request->responseMode) {
        }
    }
}
