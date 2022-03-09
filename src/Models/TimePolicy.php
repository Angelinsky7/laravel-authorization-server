<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\TimePolicyFactory;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function parent(){
        return $this->morphOne(Policy::class, 'parent', 'discriminator', 'id');
    }

    public function day_of_month(){
        return $this->belongsTo(TimeRange::class, 'day_of_month_id');
    }

    public function month(){
        return $this->belongsTo(TimeRange::class, 'month_id');
    }

    public function year(){
        return $this->belongsTo(TimeRange::class, 'year_id');
    }

    public function hour(){
        return $this->belongsTo(TimeRange::class, 'hour_id');
    }

    public function minute(){
        return $this->belongsTo(TimeRange::class, 'minute_id');
    }

    public static function newFactory()
    {
        return TimePolicyFactory::new();
    }

    public function evaluate(EvaluatorRequest $request)
    {
        //TODO(demarco): this is not correct
        $request->result = false;
        return $this->parent->evaluate($request);
    }

}
