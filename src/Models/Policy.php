<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\PolicyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

/**
 * @property-read int $id
 * @property string $name
 * @property string $description
 * @property PolicyLogic logic
 * @property Permission[] permissions
 * @property GroupPolicy $policy
 */
class Policy extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_policies';

    protected $casts = [
        'logic' => PolicyLogic::class,
    ];

    public function policy()
    {
        return $this->morphTo('policy', 'discriminator', 'id');
    }

    protected $searchable = [
        'name',
        'description',
        'logic'
    ];

    protected $fillable = [
        'name',
        'description',
        'logic'
    ];

    public static function newFactory()
    {
        return PolicyFactory::new();
    }

}
