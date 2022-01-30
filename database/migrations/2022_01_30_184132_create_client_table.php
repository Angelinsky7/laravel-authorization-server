<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientTable extends Migration
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
        $this->schema->create('clients', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled');
            $table->string('clientId')->unique();
            $table->boolean('requireClientSecret');
            $table->string('clientName')->unique();
            $table->string('description')->nullable();
            $table->string('clientUri');
            $table->enum('policyEnforcement', array_column(PolicyEnforcement::cases(), 'name'));
            $table->enum('decisionStrategy', array_column(DecisionStrategy::cases(), 'name'));
            $table->boolean('analyseModeEnabled')->default(false);
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
        Schema::dropIfExists('clients');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }
}
