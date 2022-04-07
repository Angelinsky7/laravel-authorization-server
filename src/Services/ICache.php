<?php

namespace Darkink\AuthorizationServer\Services;

use Darkink\AuthorizationServer\Helpers\Evaluator\Evaluation;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use Darkink\AuthorizationServer\Helpers\Analyse\EvaluationAnalyse;
use Darkink\AuthorizationServer\Models\Resource;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Facades\Log;

interface ICache
{
    // function setType(string $item_type);
    function get(string $key): mixed;
    function getAndSet(string $key, DateTimeInterface|DateInterval|int $duration, callable $callable): mixed;
    function set(string $key, mixed $item, DateTimeInterface|DateInterval|int $expiration);
}
