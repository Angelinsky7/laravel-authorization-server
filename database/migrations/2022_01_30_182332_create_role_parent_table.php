<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleParentTable extends Migration
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
        $this->schema->create('uma_role_role', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')
                ->references('id')
                ->on('uma_roles')
                ->onDelete('cascade');

            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')
                ->references('id')
                ->on('uma_roles')
                ->onDelete('restrict');

            $table->primary(['role_id', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_role_role');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }


}
