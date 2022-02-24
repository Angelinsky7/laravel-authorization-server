<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionPolicyTable extends Migration
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
        $this->schema->create('uma_permission_policy', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->foreign('permission_id')
                ->references('id')
                ->on('uma_permissions')
                ->onDelete('cascade');

            $table->unsignedBigInteger('policy_id');
            $table->foreign('policy_id')
                ->references('id')
                ->on('uma_policies')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'policy_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_permission_policy');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }


}
