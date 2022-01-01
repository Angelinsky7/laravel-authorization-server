<?php

namespace Darkink\AuthorizationServer\Http\Controllers;

use Darkink\AuthorizationServer\Http\Requests\Role\StoreRoleRequest;
use Darkink\AuthorizationServer\Http\Requests\Role\UpdateRoleRequest;
use Darkink\AuthorizationServer\Models\Role;
use Darkink\AuthorizationServer\Repositories\RoleRepository;
use Illuminate\Support\Facades\Log;

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

    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        $this->repo->create(
            $validated['name'],
            $validated['label'],
            $validated['description']
        );
        return redirect()->route('policy.role.index');
    }

    public function edit(Role $role)
    {
        return view('policy::Role.update', [
            'item' => $role
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $validated = $request->validated();
        $this->repo->update(
            $role,
            $validated['name'],
            $validated['label'],
            $validated['description']
        );
        return redirect()->route('policy.role.index');
    }

    public function delete(Role $role)
    {
        return view('policy::Role.delete', [
            'item' => $role
        ]);
    }

    public function destroy(Role $role)
    {
        $this->repo->delete($role);
        return redirect()->route('policy.role.index');
    }
}
