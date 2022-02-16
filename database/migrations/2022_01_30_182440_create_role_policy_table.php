<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePolicyTable extends Migration
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
        $this->schema->create('uma_role_policies', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->foreign('id')
                ->references('id')
                ->on('uma_policies')
                ->onDelete('cascade');
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
        Schema::dropIfExists('uma_role_policies');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }


}
