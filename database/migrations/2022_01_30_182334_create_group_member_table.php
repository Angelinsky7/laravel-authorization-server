<?php

use Darkink\AuthorizationServer\Database\Traits\MigrationHelperTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupMemberTable extends Migration
{
    use MigrationHelperTrait;

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
        $this->schema->create('uma_group_member', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')
                ->references('id')
                ->on('uma_groups')
                ->onDelete('cascade');

            $table->unsignedBigInteger('member_group_id')->nullable();
            $table->foreign('member_group_id')
                ->references('id')
                ->on('uma_groups')
                ->onDelete('cascade');

            $table->unsignedBigInteger('member_user_id')->nullable();
            $table->foreign('member_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['group_id', 'member_group_id', 'member_user_id']);
        });

        $this->check('uma_group_member', 'ck_uma_group_member_member', 'member_group_id IS NOT NULL OR member_user_id IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_group_member');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }
}
