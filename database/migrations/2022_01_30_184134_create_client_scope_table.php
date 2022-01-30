<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientScopeTable extends Migration
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
        $this->schema->create('client_scope', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');

            $table->unsignedBigInteger('scope_id');
            $table->foreign('scope_id')
                ->references('id')
                ->on('scopes')
                ->onDelete('cascade');

            $table->primary(['client_id', 'scope_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_scope');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }
}
