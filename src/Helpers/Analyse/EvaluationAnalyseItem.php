<?php

namespace Darkink\AuthorizationServer\Helpers\Analyse;

class EvaluationAnalyseItem {
    public string $resource_id;
    public string $resource_name;
    public bool $granted;
    public string $strategy;

    /** @var string[] $scopes */
    public array $scopes;
    /** @var EvaluationAnalysePermissionItem[] $permissions */
    public array $permissions;
}
