<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Database\Factories\ClientPolicyFactory;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @obsolete
 * @property Client[] $clients
 */
class ClientPolicy extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_client_policies';
    public $incrementing = false;
    public $timestamps = false;

    public function parent(){
        return $this->morphOne(Policy::class, 'parent', 'discriminator', 'id');
    }

    public function clients(){
        return $this->belongsToMany(Client::class, 'uma_client_policy_client', 'client_policy_id', 'client_id');
    }

    public static function newFactory()
    {
        return ClientPolicyFactory::new();
    }

    public function evaluate(EvaluatorRequest $request)
    {
        //TODO(demarco): this is not correctly implemented
        $request->result = false;
        return $this->parent->evaluate($request);
    }

}
