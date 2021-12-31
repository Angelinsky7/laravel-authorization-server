<?php

namespace Darkink\AuthorizationServer\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{

    public static $wrap = '';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $result = parent::toArray($request);

        return [
            'id' => $result['id'],
            'name' => $result['name'],
            'label' => $result['label'],
            'description' => $result['description'],
            'system' => $result['system']
        ];
    }
}
