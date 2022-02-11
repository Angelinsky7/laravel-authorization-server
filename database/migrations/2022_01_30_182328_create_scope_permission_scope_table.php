<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScopePermissionScopeTable extends Migration
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
        $this->schema->create('uma_scope_permission_scope', function (Blueprint $table) {
            $table->unsignedBigInteger('scope_permission_id');
            $table->unsignedBigInteger('resource_id');

            $table->foreign(['scope_permission_id', 'resource_id'], 'uma_scope_permission_scope_sprid_foreign')
                ->references(['id', 'resource_id'])
                ->on('uma_scope_permissions')
                ->onDelete('cascade');

            $table->unsignedBigInteger('scope_id');
            $table->foreign(['resource_id', 'scope_id'], 'uma_scope_permission_scope_rsid_foreign')
                ->references(['resource_id', 'scope_id'])
                ->on('uma_resource_scope')
                ->onDelete('restrict');

            $table->primary(['scope_permission_id', 'scope_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uma_scope_permission_scope');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }
}
