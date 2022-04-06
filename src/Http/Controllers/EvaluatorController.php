<?php

namespace Darkink\AuthorizationServer\Http\Controllers;

use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluationItem;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest as EvaluatorEvaluatorRequest;
use Darkink\AuthorizationServer\Helpers\KeyValuePair;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluatorRequest;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluatorRequestResponseMode;
use Darkink\AuthorizationServer\Http\Resources\AuthorizationResource;
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

        $client_id_request = $validated['client_id'];
        $client_id_token = $request->user()->token()['client_id'];

        if ($client_id_request != $client_id_token) {
            $error = ValidationException::withMessages([
                'client' => ["clientId given ($client_id_token) is not the same as the clientId is the token ($client_id_request)"],
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
                    $group_by_resources = array_group($evaluation->results, fn (EvaluationItem $p) => $p->rs_name);

                    foreach ($evaluatorRequest->permission_resource_scope_items as $request_permission) {
                        if ($request_permission->resource_name != null && $request_permission->scope_name == null) {
                            $granted[] = array_any($group_by_resources, fn (KeyValuePair $p) => $p->key == $request_permission->resource_name);
                        } elseif ($request_permission->resource_name != null && $request_permission->scope_name != null) {
                            $granted[] = array_any($group_by_resources, fn (KeyValuePair $p) => $p->key == $request_permission->resource_name && array_any($p->value, fn (EvaluationItem $a) => array_any($a->scopes, fn (string $m) => $m == $request_permission->scope_name)));
                        } elseif ($request_permission->resource_name == null && $request_permission->scope_name != null) {
                            $granted[] = array_any($group_by_resources, fn (KeyValuePair $p) => array_any($p->value, fn (EvaluationItem $a) => array_any($a->scopes, fn (string $m) => $m == $request_permission->scope_name)));
                        } else {
                            $error = ValidationException::withMessages([
                                'resource' => ["Requested resource is empty"],
                            ]);
                            throw $error;
                        }
                    }

                    return new AuthorizationResource([
                        'aud' => $client->id,
                        'sub' => $request->user()->id,
                        'results' => array_count($granted, fn ($p) => !$p) == 0
                    ], $client->json_mode_enabled);
                }
                break;
            case EvaluatorRequestResponseMode::PERMISSIONS: {
                    return new AuthorizationResource([
                        'aud' => $client->oauth->id,
                        'sub' => $request->user()->id,
                        'permissions' => $evaluation->results_only_with_scopes()
                    ], $client->json_mode_enabled);
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

        return null;
    }
}
