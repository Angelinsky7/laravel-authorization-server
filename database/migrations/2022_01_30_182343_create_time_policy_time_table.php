<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimePolicyTimeTable extends Migration
{
    protected $schema;


    public function __construct()
    {
        $this->schema = Schema::connection($this->getConnection());

    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('uma_time_policy_time', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('time_policy_id');
            $table->foreign('time_policy_id')
                ->references('id')
                ->on('uma_time_policies')
                ->onDelete('cascade');

            $table->unsignedBigInteger('day_of_month_id');
            $table->foreign('day_of_month_id')
                ->references('id')
                ->on('uma_timeranges')
                ->onDelete('cascade');

            $table->unsignedBigInteger('month_id');
            $table->foreign('month_id')
                ->references('id')
                ->on('uma_timeranges')
                ->onDelete('cascade');

            $table->unsignedBigInteger('year_id');
            $table->foreign('year_id')
                ->references('id')
                ->on('uma_timeranges')
                ->onDelete('cascade');

            $table->unsignedBigInteger('hour_id');
            $table->foreign('hour_id')
                ->references('id')
                ->on('uma_timeranges')
                ->onDelete('cascade');

            $table->unsignedBigInteger('minute_id');
            $table->foreign('minute_id')
                ->references('id')
                ->on('uma_timeranges')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_time_policy_time');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }


}
