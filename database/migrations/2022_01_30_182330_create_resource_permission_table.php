<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourcePermissionTable extends Migration
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
        $this->schema->create($this->prefix . 'resource_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->foreign('id')
                ->references('id')
                ->on($this->prefix . 'permissions')
                ->onDelete('cascade');
            $table->primary(['id']);

            $table->string('resource_type')->unique();

            $table->unsignedBigInteger('resource_id')->nullable();
            $table->foreign('resource_id')
                ->references('id')
                ->on($this->prefix . 'resources')
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
        Schema::dropIfExists($this->prefix . 'resource_permissions');
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
