<?php

namespace Darkink\AuthorizationServer\Http\Requests\Evaluator;

enum EvaluatorRequestResponseMode : string {
    case DECISION = 'decision';
    case PERMISSIONS = 'permissions';
    case ANALYSE = 'analyse';
}
