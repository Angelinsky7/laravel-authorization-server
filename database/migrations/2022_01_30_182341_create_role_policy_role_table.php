<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePolicyRoleTable extends Migration
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
        $this->schema->create('uma_role_policy_role', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('role_policy_id');
            $table->foreign('role_policy_id')
                ->references('id')
                ->on('uma_role_policies')
                ->onDelete('cascade');

            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')
                ->references('id')
                ->on('uma_roles')
                ->onDelete('cascade');

            $table->unique(['role_policy_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_role_policy_role');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }


}
