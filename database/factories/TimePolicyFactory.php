<?php

namespace Darkink\AuthorizationServer\Database\Factories;

use App\Models\User;
use Darkink\AuthorizationServer\Models\Policy;
use Darkink\AuthorizationServer\Models\TimePolicy;
use Darkink\AuthorizationServer\Models\TimeRange;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TimePolicyFactory extends Factory
{

    protected $model = TimePolicy::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [];
    }

    public function configure()
    {
        return $this->afterMaking(function (TimePolicy $policy) {
            /** @var Policy $parent */
            $parent = Policy::factory()->make();
            $parent->discriminator = get_class($policy);
            $parent->save();
            $policy->id = $parent->id;
        })->afterCreating(function (TimePolicy $policy) {
            if ($this->faker->boolean()) {
                $policy->not_before = $this->faker->dateTimeBetween('now', '+1 years');
            }
            if ($this->faker->boolean()) {
                $policy->not_after = $this->faker->dateTimeBetween('+2 years', '+3 years');
            }
            if ($this->faker->boolean()) {
                $policy->year()->associate($this->_createTimerange($this->faker->numberBetween(1980, 2021), $this->faker->numberBetween(2022, 2034)));
            }
            if ($this->faker->boolean()) {
                $policy->day_of_month()->associate($this->_createTimerange($this->faker->numberBetween(1, 3), $this->faker->numberBetween(4, 7)));
            }
            if ($this->faker->boolean()) {
                $policy->month()->associate($this->_createTimerange($this->faker->numberBetween(1, 5), $this->faker->numberBetween(6, 12)));
            }
            if ($this->faker->boolean()) {
                $policy->hour()->associate($this->_createTimerange($this->faker->numberBetween(1, 12), $this->faker->numberBetween(13, 23)));
            }
            if ($this->faker->boolean()) {
                $policy->minute()->associate($this->_createTimerange($this->faker->numberBetween(1, 30), $this->faker->numberBetween(31, 59)));
            }
            $policy->save();
        });
    }

    private function _createTimerange(int $from, int $to)
    {
        $result = new TimeRange();
        $result->from = $from;
        $result->to = $to;
        $result->save();
        return $result;
    }
}
