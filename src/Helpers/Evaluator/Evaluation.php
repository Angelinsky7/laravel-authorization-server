<?php

namespace Darkink\AuthorizationServer\Helpers\Evaluator;

class Evaluation
{
    /** @var EvaluationItem[] $result */
    public array $results = [];

    public function results_only_with_scopes()
    {
        $result = $this->results;
        $result = array_filter($result, fn (EvaluationItem $p) => count($p->scopes) != 0);
        return $result;
    }
}
