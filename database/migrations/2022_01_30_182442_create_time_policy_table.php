<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimePolicyTable extends Migration
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
        $this->schema->create('uma_time_policies', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->foreign('id')
                ->references('id')
                ->on('uma_policies')
                ->onDelete('cascade');

            $table->dateTime('not_before')->nullable();
            $table->dateTime('not_after')->nullable();

            $table->unsignedBigInteger('day_of_month_id')->nullable();
            $table->foreign('day_of_month_id')
                ->references('id')
                ->on('uma_timeranges')
                ->onDelete('restrict');

            $table->unsignedBigInteger('month_id')->nullable();
            $table->foreign('month_id')
                ->references('id')
                ->on('uma_timeranges')
                ->onDelete('restrict');

            $table->unsignedBigInteger('year_id')->nullable();
            $table->foreign('year_id')
                ->references('id')
                ->on('uma_timeranges')
                ->onDelete('restrict');

            $table->unsignedBigInteger('hour_id')->nullable();
            $table->foreign('hour_id')
                ->references('id')
                ->on('uma_timeranges')
                ->onDelete('restrict');

            $table->unsignedBigInteger('minute_id')->nullable();
            $table->foreign('minute_id')
                ->references('id')
                ->on('uma_timeranges')
                ->onDelete('restrict');

            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_time_policies');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }
}
