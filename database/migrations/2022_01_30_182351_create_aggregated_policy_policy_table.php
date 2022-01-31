<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAggregatedPolicyPolicyTable extends Migration
{
    protected $schema;
    protected $prefix;

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
        $this->schema->create($this->prefix . 'aggregated_policy_policy', function (Blueprint $table) {
            $table->unsignedBigInteger('aggregated_policy_id');
            $table->foreign('aggregated_policy_id')
                ->references('id')
                ->on($this->prefix . 'aggregated_policies')
                ->onDelete('cascade');

            $table->unsignedBigInteger('policy_id');
            $table->foreign('policy_id')
                ->references('id')
                ->on($this->prefix . 'policies')
                ->onDelete('cascade');

            $table->primary(['aggregated_policy_id', 'policy_id'], $this->prefix . 'aggregated_policy_policy_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->prefix . 'aggregated_policy_policy');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }

    public function getPrefix()
    {
        return config('policy.storage.database.prefix');
    }
}
