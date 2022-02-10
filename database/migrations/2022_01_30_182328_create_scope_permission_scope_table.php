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
            $table->foreign('scope_permission_id')
                ->references('id')
                ->on('uma_scope_permissions')
                ->onDelete('cascade');

            //TODO(demarco): We should not have uma_scopes as a base table for this
            //               We should instead use uma_resource_scope because when a Resource
            //               will change it's scopes it will be reflected there...
            // {
            $table->unsignedBigInteger('scope_id');
            $table->foreign('scope_id')
                ->references('id')
                ->on('uma_scopes')
                ->onDelete('restrict');
            // }

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
