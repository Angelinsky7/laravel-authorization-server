<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourcePermissionTable extends Migration
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
        $this->schema->create('uma_resource_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->foreign('id')
                ->references('id')
                ->on('uma_permissions')
                ->onDelete('cascade');
            $table->primary(['id']);

            $table->string('resource_type')->unique();

            $table->unsignedBigInteger('resource_id')->nullable();
            $table->foreign('resource_id')
                ->references('id')
                ->on('uma_resources')
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
        Schema::dropIfExists('uma_resource_permissions');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }


}
