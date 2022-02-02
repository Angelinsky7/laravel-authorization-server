<?php

use Darkink\AuthorizationServer\Models\PolicyEnforcement;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
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
        $this->schema->create('uma_clients', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled');
            $table->string('clientId')->unique();
            $table->boolean('require_client_secret');
            $table->string('client_name')->unique();
            $table->string('description')->nullable();
            $table->string('client_uri');
            $table->enum('policy_enforcement', array_slice(array_column(PolicyEnforcement::cases(), 'value'), 1));
            $table->enum('decision_strategy', array_slice(array_column(DecisionStrategy::cases(), 'value'), 1));
            $table->boolean('analyse_mode_enabled')->default(false);
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
        Schema::dropIfExists('uma_clients');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }
}
