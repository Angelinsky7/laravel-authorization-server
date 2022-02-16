<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPolicyUserTable extends Migration
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
        $this->schema->create('uma_user_policy_user', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_policy_id');
            $table->foreign('user_policy_id')
                ->references('id')
                ->on('uma_user_policies')
                ->onDelete('cascade');

            $table->string('user');

            $table->unique(['user_policy_id', 'user']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_user_policy_user');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }


}
