<?php

namespace Darkink\AuthorizationServer\Services\Caching;

use Darkink\AuthorizationServer\Helpers\Analyse\EvaluationAnalyse;
use Darkink\AuthorizationServer\Helpers\Evaluator\Evaluation;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluatorRequestResponseMode;
use Darkink\AuthorizationServer\Http\Resources\AuthorizationResource;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Services\ICache;
use Darkink\AuthorizationServer\Services\IEvaluatorService;

class CachingEvaluatorService implements IEvaluatorService
{

    private IEvaluatorService $_inner;
    private ICache $_cache;

    public function __construct(IEvaluatorService $inner, ICache $cache)
    {
        $this->_inner = $inner;
        $this->_cache = $cache;
    }

    public function hanlde(EvaluatorRequest $request, EvaluatorRequestResponseMode $response_mode): AuthorizationResource | array | null
    {

        // //TODO(demarco): this is far from good... but we can test it like that
        $key = "{$request->client->oauth_id}:{$request->user->token()['id']}";
        $result = $this->_cache->getAndSet(
            $key,
            900,
            fn () => $this->_inner->hanlde($request, $response_mode)
        );

        return $result;

        // return $this->_inner->hanlde($request, $response_mode);
    }

    public function evaluate(EvaluatorRequest $request): EvaluatorRequest
    {
        return $this->_inner->evaluate($request);
    }
    public function buildEvaluationAnalyse(EvaluatorRequest $request): EvaluationAnalyse
    {
        return $this->_inner->buildEvaluationAnalyse($request);
    }
    public function buildEvaluation(EvaluatorRequest $request, Resource $filterResouce = null): Evaluation
    {
        return $this->_inner->buildEvaluation($request, $filterResouce);
    }
}
