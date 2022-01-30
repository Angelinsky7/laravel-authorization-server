<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceScopeTable extends Migration
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
        $this->schema->create('resource_scope', function (Blueprint $table) {
            $table->unsignedBigInteger('resource_id');
            $table->foreign('resource_id')
                ->references('id')
                ->on('resources')
                ->onDelete('cascade');

            $table->unsignedBigInteger('scope_id');
            $table->foreign('scope_id')
                ->references('id')
                ->on('scopes')
                ->onDelete('cascade');

            $table->primary(['resource_id', 'scope_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_scope');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }
}
