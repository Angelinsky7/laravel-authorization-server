<?php

namespace Darkink\AuthorizationServer\Http\Controllers;

use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest as EvaluatorEvaluatorRequest;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluatorRequest;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluatorRequestResponseMode;
use Darkink\AuthorizationServer\Repositories\ClientRepository;
use Darkink\AuthorizationServer\Services\IEvaluatorService;
use Illuminate\Validation\ValidationException;

class EvaluatorController
{

    protected IEvaluatorService $evaluator;
    protected ClientRepository $clientRepository;

    public function __construct(IEvaluatorService $evaluator, ClientRepository $clientRepository)
    {
        $this->evaluator = $evaluator;
        $this->clientRepository = $clientRepository;
    }

    public function process(EvaluatorRequest $request)
    {
        $validated = $request->validated();

        // $client = await _clientStore.GetFromClientIdAsync(permissionRequest.ClientId);

        $client = $this->clientRepository->find($validated['client_id']);

        if ($client == null || !$client->enabled) {
            $client_id_error_message = $validated['client_id'];
            $error = ValidationException::withMessages([
                'client' => ["No client found with the clientId given: $client_id_error_message"],
            ]);
            throw $error;
        }

        $evaluatorRequest = new EvaluatorEvaluatorRequest($client, $request->user(), $validated['permission'] ?? []);
        $this->evaluator->evaluate($evaluatorRequest);
        $evaluation = $this->evaluator->buildEvaluation($evaluatorRequest);

        switch ($validated['response_mode']) {
            case EvaluatorRequestResponseMode::DECISION: {
                    /** @var bool[] */
                    $granted = [];
                    $groupByResources = array_group($evaluation->results, fn ($p) => $p->rs_name);

                    foreach ($evaluatorRequest->permissionResourceScopeItems as $requestPermission) {
                        if ($requestPermission->resource_name != null && $requestPermission->scope_name == null) {
                            $granted[] = array_any($groupByResources, fn ($p) => $p->key == $requestPermission->resource_name);
                        } elseif ($requestPermission->resource_name != null && $requestPermission->scope_name != null) {
                            $granted[] = array_any($groupByResources, fn ($p) => $p->key == $requestPermission->resource_name && array_any($p->scopes, fn ($a) => $a == $requestPermission->scope_name));
                        } elseif ($requestPermission->resource_name == null && $requestPermission->scope_name != null) {
                            $granted[] = array_any($groupByResources, fn ($p) => array_any($p->scopes, fn ($a) => $a == $requestPermission->scope_name));
                        } else {
                            $error = ValidationException::withMessages([
                                'resource' => ["Requested resource is empty"],
                            ]);
                            throw $error;
                        }
                    }

                    return [
                        'client_id' => $client->id,
                        'results' => array_count($granted, fn ($p) => !$p) == 0
                    ];
                }
                break;
            case EvaluatorRequestResponseMode::PERMISSIONS: {
                    return [
                        'client_id' => $client->id,
                        'results' => $evaluation->results->only_with_scopes()
                    ];
                }
                break;
            case EvaluatorRequestResponseMode::ANALYSE: {
                    if (!$client->analyse_mode_enabled) {
                        $error = ValidationException::withMessages([
                            'client' => ["client does not permit Analyse. See the log."],
                        ]);
                        throw $error;
                    }
                    $analyse = $this->evaluator->buildEvaluationAnalyse($evaluatorRequest);
                    return [
                        'analyse' => $analyse->items
                    ];
                }
                break;
        }

        return 'opk';
        return null;
    }
}
