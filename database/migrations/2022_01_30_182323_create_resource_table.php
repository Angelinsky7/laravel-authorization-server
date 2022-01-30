<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceTable extends Migration
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
        $this->schema->create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('displayName');
            $table->string('type')->nullable();
            $table->string('iconUri')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resources');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }
}
