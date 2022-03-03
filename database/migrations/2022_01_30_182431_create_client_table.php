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
            $table->id('id');
            $table->char('oauth_id', 36);
            $table->boolean('enabled');
            $table->string('client_id')->unique();
            $table->boolean('require_client_secret');
            $table->string('client_name')->unique();
            $table->string('description')->nullable();
            $table->string('client_uri');
            $table->enum('policy_enforcement', array_slice(array_column(PolicyEnforcement::cases(), 'value'), 1))->default(PolicyEnforcement::Enforcing->value);
            $table->enum('decision_strategy', array_slice(array_column(DecisionStrategy::cases(), 'value'), 1))->default(DecisionStrategy::Affirmative->value);
            $table->boolean('analyse_mode_enabled')->default(false);
            $table->char('permission_splitter', 1)->default('#');
            $table->timestamps();

            $table->unique('oauth_id', 'uma_clients_oauth_unique');
            $table->unique('client_id', 'uma_clients_client_unique');

            $table->foreign('oauth_id')
                ->references('id')
                ->on('oauth_clients')
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
        Schema::dropIfExists('uma_clients');
    }

    public function getConnection()
    {
        return config('policy.storage.database.connection');
    }
}
