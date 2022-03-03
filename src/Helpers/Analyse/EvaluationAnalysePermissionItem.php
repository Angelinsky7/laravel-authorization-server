<?php

namespace Darkink\AuthorizationServer\Helpers\Analyse;

class EvaluationAnalysePermissionItem
{
    public string $permission_id;
    public string $permission_name;
    public string $strategy;
    public bool $granted;

    /** @var string[] $scopes */
    public array $scopes = [];
    /** @var EvaluationAnalysePolicyItem[] $policies */
    public array $policies = [];

    public function __construct(string $permission_id, string $permission_name, string $strategy, bool $granted, array $scopes)
    {
        $this->permission_id = $permission_id;
        $this->permission_name = $permission_name;
        $this->strategy = $strategy;
        $this->granted = $granted;
        $this->scopes = $scopes;
    }

}
