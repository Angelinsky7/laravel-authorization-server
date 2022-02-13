<?php

namespace Darkink\AuthorizationServer\Http\Requests\Permission;

use Illuminate\Validation\Rule;

class UpdateResourcePermissionRequest extends StoreResourcePermissionRequest
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
