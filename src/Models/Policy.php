<?php

namespace Darkink\AuthorizationServer\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

/**
 * @property-read int $id
 * @property string $name
 * @property string $description
 * @property PolicyLogic logic
 */
class Policy extends BaseModel
{
    protected $table = 'uma_policies';

    protected $child_classes = [
        AggregatePolicy::class,
        ClientPolicy::class
    ];

    protected $searchable = [
        'name',
        'description',
        'logic'
    ];

    protected $fillable = ['name', 'description', 'logic'];


}
