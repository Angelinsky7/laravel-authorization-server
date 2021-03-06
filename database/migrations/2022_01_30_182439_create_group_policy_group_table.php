<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupPolicyGroupTable extends Migration
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
        $this->schema->create('uma_group_policy_group', function (Blueprint $table) {
            $table->unsignedBigInteger('group_policy_id');
            $table->foreign('group_policy_id')
                ->references('id')
                ->on('uma_group_policies')
                ->onDelete('cascade');

            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')
                ->references('id')
                ->on('uma_groups')
                ->onDelete('cascade');

            $table->primary(['group_policy_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_group_policy_group');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }
}
