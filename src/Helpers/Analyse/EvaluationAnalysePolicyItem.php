<?php

namespace Darkink\AuthorizationServer\Helpers\Analyse;

class EvaluationAnalysePolicyItem
{
    public string $policy_id;
    public string $policy_name;
    public bool $granted;

    public function __construct(string $policy_id, string $policy_name, bool $granted = false)
    {
        $this->policy_id = $policy_id;
        $this->policy_name = $policy_name;
        $this->granted = $granted;
    }

}
