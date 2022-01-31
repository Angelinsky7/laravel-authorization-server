<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientResourceTable extends Migration
{
    protected $schema;
    protected $prefix;

    public function __construct()
    {
        $this->schema = Schema::connection($this->getConnection());
        $this->prefix= $this->getPrefix();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create($this->prefix . 'client_resource', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')
                ->references('id')
                ->on($this->prefix . 'clients')
                ->onDelete('cascade');

            $table->unsignedBigInteger('resource_id');
            $table->foreign('resource_id')
                ->references('id')
                ->on($this->prefix . 'resources')
                ->onDelete('cascade');

            $table->primary(['client_id', 'resource_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->prefix . 'client_resource');
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
