<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\PolicyFactory;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

/**
 * @property-read int $id
 * @property string $name
 * @property string $description
 * @property PolicyLogic logic
 * @property bool $is_system
 * @property Permission[] permissions
 * @property GroupPolicy | RolePolicy | UserPolicy | ClientPolicy | TimePolicy | AggregatedPolicy $policy
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

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'uma_permission_policy', 'policy_id', 'permission_id');
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

    public function evaluate(EvaluatorRequest $request)
    {
        if ($this->logic == PolicyLogic::Negative) {
            $request->result = !$request->result;
        }
        return $request->result;
    }

}
