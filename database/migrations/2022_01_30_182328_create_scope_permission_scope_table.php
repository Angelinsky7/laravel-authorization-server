<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScopePermissionScopeTable extends Migration
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
        $this->schema->create($this->prefix . 'scope_permission_scope', function (Blueprint $table) {
            $table->unsignedBigInteger('scope_permission_id');
            $table->foreign('scope_permission_id')
                ->references('id')
                ->on($this->prefix . 'scope_permissions')
                ->onDelete('cascade');

            $table->unsignedBigInteger('scope_id');
            $table->foreign('scope_id')
                ->references('id')
                ->on($this->prefix . 'scopes')
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
        Schema::dropIfExists($this->prefix . 'scope_permission_scope');
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
