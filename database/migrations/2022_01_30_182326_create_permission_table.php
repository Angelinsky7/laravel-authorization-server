<?php

use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionTable extends Migration
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
        $this->schema->create('uma_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->enum('decision_strategy', array_slice(array_column(DecisionStrategy::cases(), 'value'), 1));
            $table->boolean('is_system')->default(false);
            $table->string('discriminator');
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
        Schema::dropIfExists('uma_permissions');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }
}
