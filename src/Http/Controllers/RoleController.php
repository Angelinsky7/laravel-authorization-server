<?php

namespace Darkink\AuthorizationServer\Http\Controllers;

use Darkink\AuthorizationServer\Repositories\RoleRepository;

class RoleController
{
    protected RoleRepository $repo;

    public function __construct(RoleRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $items = $this->repo->gets();

        return view('policy::Role.index', [
            'items' => $items
        ]);
    }

    public function create()
    {
        return view('policy::Role.create');
    }
}
