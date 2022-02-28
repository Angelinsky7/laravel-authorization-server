<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientPermissionTable extends Migration
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
        $this->schema->create('uma_client_permission', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')
                ->references('id')
                ->on('uma_clients')
                ->onDelete('cascade');

            $table->unsignedBigInteger('permission_id');
            $table->foreign('permission_id')
                ->references('id')
                ->on('uma_permissions')
                ->onDelete('cascade');

            $table->primary(['client_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_client_permission');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }


}
