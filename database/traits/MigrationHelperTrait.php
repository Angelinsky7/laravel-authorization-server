<?php

namespace Darkink\AuthorizationServer\Database\Traits;

use Illuminate\Support\Facades\DB;

trait MigrationHelperTrait {

    function check(string $table, string $checkName, string $expr){
        DB::statement("ALTER TABLE $table ADD CONSTRAINT $checkName CHECK ($expr);");
    }

}
