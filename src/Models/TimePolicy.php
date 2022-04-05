<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\TimePolicyFactory;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Date;

/**
 * @property DateTime $not_before
 * @property DateTime $not_after
 * @property TimeRange $day_of_month
 * @property TimeRange $month
 * @property TimeRange $year
 * @property TimeRange $hour
 * @property TimeRange $minute
 */
class TimePolicy extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_time_policies';
    public $incrementing = false;
    public $timestamps = false;

    public function parent()
    {
        return $this->morphOne(Policy::class, 'parent', 'discriminator', 'id');
    }

    public function day_of_month()
    {
        return $this->belongsTo(TimeRange::class, 'day_of_month_id');
    }

    public function month()
    {
        return $this->belongsTo(TimeRange::class, 'month_id');
    }

    public function year()
    {
        return $this->belongsTo(TimeRange::class, 'year_id');
    }

    public function hour()
    {
        return $this->belongsTo(TimeRange::class, 'hour_id');
    }

    public function minute()
    {
        return $this->belongsTo(TimeRange::class, 'minute_id');
    }

    public static function newFactory()
    {
        return TimePolicyFactory::new();
    }

    protected $casts = [
        'not_before' => 'datetime',
        'not_after' => 'datetime',
    ];

    public function evaluate(EvaluatorRequest $request)
    {
        $dateTimeNow = new DateTime();
        $result = true;

        $day = (int)$dateTimeNow->format('d');
        $month = (int)$dateTimeNow->format('m');
        $year = (int)$dateTimeNow->format('Y');
        $hour = (int)$dateTimeNow->format('H');
        $minute = (int)$dateTimeNow->format('i');

        if ($this->not_before != null && $dateTimeNow < $this->not_before) {
            $result = false;
        }
        if ($result != false && ($this->not_after != null && $dateTimeNow >= $this->not_after)) {
            $result = false;
        }
        if ($result != false && ($this->day_of_month != null && ($day < $this->day_of_month->from || $day >= $this->day_of_month->to))) {
            $result = false;
        }
        if ($result != false && ($this->month != null && ($month < $this->month->from || $month >= $this->month->to))) {
            $result = false;
        }
        if ($result != false && ($this->year != null && ($year < $this->year->from || $year >= $this->year->to))) {
            $result = false;
        }
        if ($result != false && ($this->hour != null && ($hour < $this->hour->from || $hour >= $this->hour->to))) {
            $result = false;
        }
        if ($result != false && ($this->minute != null && ($minute < $this->minute->from || $minute >= $this->minute->to))) {
            $result = false;
        }

        $request->result = $result;
        return $this->parent->evaluate($request);
    }
}
