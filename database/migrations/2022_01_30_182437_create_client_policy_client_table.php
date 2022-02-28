<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientPolicyClientTable extends Migration
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
        $this->schema->create('uma_client_policy_client', function (Blueprint $table) {
            $table->unsignedBigInteger('client_policy_id');
            $table->foreign('client_policy_id')
                ->references('id')
                ->on('uma_client_policies')
                ->onDelete('cascade');

            // $table->unsignedBigInteger('client_id');
            $table->char('client_id', 36);

            $table->foreign('client_id')
                ->references('id')
                ->on('oauth_clients')
                ->onDelete('cascade');

            $table->primary(['client_policy_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_policy_client');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }


}
