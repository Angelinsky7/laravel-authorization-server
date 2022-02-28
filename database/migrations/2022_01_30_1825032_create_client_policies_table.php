<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientPoliciesTable extends Migration
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
        $this->schema->create('uma_client_policy', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')
                ->references('id')
                ->on('uma_clients')
                ->onDelete('cascade');

            $table->unsignedBigInteger('policy_id');
            $table->foreign('policy_id')
                ->references('id')
                ->on('uma_policies')
                ->onDelete('cascade');

            $table->primary(['client_id', 'policy_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_client_policy');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }


}
