<?php

namespace Darkink\AuthorizationServer\Helpers\Evaluator;

class EvaluationItem
{
    public string $rs_id;
    public string $rs_name;

    /** @var string[] $scope */
    public array $scopes;

    public function __construct(string $rd_id, string $rs_name, array $scope = [])
    {
        $this->rs_id = $rd_id;
        $this->rs_name = $rs_name;
        $this->scopes = $scope;
    }
}
