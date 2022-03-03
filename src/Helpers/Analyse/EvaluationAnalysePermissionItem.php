<?php

namespace Darkink\AuthorizationServer\Helpers\Analyse;

class EvaluationAnalysePermissionItem
{
    public string $permission_id;
    public string $permission_name;
    public bool $granted;
    public string $strategy;

    /** @var string[] $scopes */
    public array $scopes;
    /** @var EvaluationAnalysePolicyItem[] $policies */
    public array $policies;
}
