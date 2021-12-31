<?php

namespace Darkink\AuthorizationServer\Http\Controllers;

use Darkink\AuthorizationServer\Http\Resources\RoleResource;
use Darkink\AuthorizationServer\Repositories\RoleRepository;

class ApiRoleController
{
    protected RoleRepository $repo;

    public function __construct(RoleRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $items = $this->repo->gets();
        $result = RoleResource::collection($items);
        $result->withoutWrapping();
        return $result;
    }
}
