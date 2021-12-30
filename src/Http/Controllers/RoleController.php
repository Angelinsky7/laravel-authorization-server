<?php

namespace Darkink\AuthorizationServer\Http\Controllers;

class RoleController
{

    public function index()
    {
        return view('policy::Role.index', [
            'items' => ['f', 'g', 'h']
        ]);
    }
}
