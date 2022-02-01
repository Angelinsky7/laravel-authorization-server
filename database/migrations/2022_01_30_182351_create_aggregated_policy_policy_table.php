<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAggregatedPolicyPolicyTable extends Migration
{
    protected $schema;


    public function __construct()
    {
        $this->schema = Schema::connection($this->getConnection());
        $this->prefix = $this->getPrefix();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('uma_aggregated_policy_policy', function (Blueprint $table) {
            $table->unsignedBigInteger('aggregated_policy_id');
            $table->foreign('aggregated_policy_id')
                ->references('id')
                ->on('uma_aggregated_policies')
                ->onDelete('cascade');

            $table->unsignedBigInteger('policy_id');
            $table->foreign('policy_id')
                ->references('id')
                ->on('uma_policies')
                ->onDelete('cascade');

            $table->primary(['aggregated_policy_id', 'policy_id'], 'uma_aggregated_policy_policy_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_aggregated_policy_policy');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }


}
