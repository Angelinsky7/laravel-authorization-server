<?php

namespace Darkink\AuthorizationServer\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Rules\IsResource;
use Darkink\AuthorizationServer\Rules\IsScope;
use Illuminate\Validation\Rule;

class UpdateScopePermissionRequest extends StoreScopePermissionRequest
{
    use RequestPermissionTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            $this->permission_update_rules()
        );
    }
}
