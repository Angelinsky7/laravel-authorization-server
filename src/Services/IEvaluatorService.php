<?php

namespace Darkink\AuthorizationServer\Services;

use Darkink\AuthorizationServer\Helpers\Evaluator\Evaluation;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use Darkink\AuthorizationServer\Helpers\Analyse\EvaluationAnalyse;
use Darkink\AuthorizationServer\Models\Resource;

interface IEvaluatorService {
    function evaluate(EvaluatorRequest $request): EvaluatorRequest;
    function buildEvaluationAnalyse(EvaluatorRequest $request): EvaluationAnalyse;
    function buildEvaluation(EvaluatorRequest $request, Resource $filterResouce = null): Evaluation;
}
