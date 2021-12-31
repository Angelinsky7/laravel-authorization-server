<?php

namespace Darkink\AuthorizationServer\Console;

use Darkink\AuthorizationServer\Models\Role;
use Darkink\AuthorizationServer\Repositories\RoleRepository;
use Illuminate\Console\Command;

class RoleCommand extends Command
{
    protected $signature = 'policy:role
                            {--name= : The name of the role}
                            {--label= : The label of the role}
                            {--description= : The description of the role}
                            {--system= : Is a system role}';

    protected $description = 'Create a role';

    public function handle(RoleRepository $repo)
    {
        $name = $this->option('name') ?: $this->ask('What should we name the role ?');
        $label = $this->option('label') ?: $this->ask('What should we label the role ?', $name);
        $description = $this->option('description') ?: $this->ask('What should we description the role ?');
        $system = $this->option('system') ?: $this->ask('What should be system property of the role ?', 0);

        $role = $repo->create(
            $name,
            $label,
            $description,
            $system
        );

        $this->info('Role created successfully.');

        $this->outputRoleDetails($role);
    }

    protected function outputRoleDetails(Role $role)
    {
        $this->line('<comment>Role id:</comment> ' . $role->id);
        $this->line('<comment>Role name:</comment> ' . $role->name);
        $this->line('<comment>Role label:</comment> ' . $role->label);
        $this->line('<comment>Role description:</comment> ' . $role->description);
        $this->line('<comment>Role system:</comment> ' . $role->system);
    }
}
