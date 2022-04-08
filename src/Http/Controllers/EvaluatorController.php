<?php

namespace Darkink\AuthorizationServer\Http\Controllers;

use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest as EvaluatorEvaluatorRequest;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluatorRequest;
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

        return $this->evaluator->hanlde($evaluatorRequest, $validated['response_mode']);
    }
}
