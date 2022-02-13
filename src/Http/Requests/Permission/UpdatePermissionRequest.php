<?php

namespace Darkink\AuthorizationServer\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends StorePermissionRequest
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
