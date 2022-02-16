<?php

namespace Darkink\AuthorizationServer\Rules;

use Darkink\AuthorizationServer\Models\Role;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\DatabaseRule;

class IsRoleCyclicParent implements Rule
{
    protected Role $role;

    public function __construct(array $attributes)
    {
        unset($attributes['_token']);
        $this->role = (new Role())->forceFill($attributes);
    }

    public function passes($attribute, $value)
    {
        return $this->checkCircualReferences($this->role);
    }

    protected function checkCircualReferences(Role $role, array &$visitedRoles = null)
    {
        if ($visitedRoles == null) {
            $visitedRoles = [];
        }

        if (in_array($role->id, $visitedRoles)) {
            return false;
        }

        $visitedRoles[] = $role->id;

        foreach ($role->parents()->get() as $parent) {
            if (!$this->checkCircualReferences($parent, $visitedRoles)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The role has a cyclique dependency tree.';
    }
}
