<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use DateTime;

/**
 * @property DateTime $notBefore
 * @property DateTime $notAfter
 * @property TimeRange $dayOfMonth
 * @property TimeRange $month
 * @property TimeRange $year
 * @property TimeRange $hour
 * @property TimeRange $minute
 */
class TimePolicy extends BaseModel
{

    protected $table = 'uma_time_policies';

}
