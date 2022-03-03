<?php

namespace Darkink\AuthorizationServer\Helpers\Analyse;

class EvaluationAnalyseItem {
    public string $resource_id;
    public string $resource_name;
    public bool $granted;
    public string $strategy;

    /** @var string[] $scopes */
    public array $scopes = [];
    /** @var EvaluationAnalysePermissionItem[] $permissions */
    public array $permissions = [];

    public function __construct(string $resource_id, string $resource_name, string $strategy)
    {
        $this->resource_id = $resource_id;
        $this->resource_name = $resource_name;
        $this->strategy = $strategy;
    }

}
