<?php

namespace Darkink\AuthorizationServer\Models;

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
class TimePolicy extends Policy
{
    public function __construct()
    {
        $this->table = config('policy.storage.database.prefix') . 'time_policies';
    }
}
