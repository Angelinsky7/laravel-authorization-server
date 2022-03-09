<?php

namespace Darkink\AuthorizationServer\Services\_Default;

use Darkink\AuthorizationServer\Helpers\Evaluator\Evaluation;
use Darkink\AuthorizationServer\Helpers\Analyse\EvaluationAnalyse;
use Darkink\AuthorizationServer\Helpers\Analyse\EvaluationAnalyseItem;
use Darkink\AuthorizationServer\Helpers\Analyse\EvaluationAnalysePermissionItem;
use Darkink\AuthorizationServer\Helpers\Analyse\EvaluationAnalysePolicyItem;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluationItem;
use Darkink\AuthorizationServer\Helpers\Evaluator\EvaluatorRequest;
use Darkink\AuthorizationServer\Helpers\Evaluator\PermissionDecision;
use Darkink\AuthorizationServer\Helpers\Evaluator\PermissionResourceScopeItem;
use Darkink\AuthorizationServer\Helpers\Evaluator\ResourceScopeResult;
use Darkink\AuthorizationServer\Helpers\KeyValuePair;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Policy;
use Darkink\AuthorizationServer\Models\PolicyEnforcement;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\ResourcePermission;
use Darkink\AuthorizationServer\Models\Scope;
use Darkink\AuthorizationServer\Models\ScopePermission;
use Darkink\AuthorizationServer\Policy as AuthorizationServerPolicy;
use Darkink\AuthorizationServer\Services\IEvaluatorService;
use Error;
use Illuminate\Support\Collection;

class DefaultEvaluatorService implements IEvaluatorService
{

    public function Evaluate(EvaluatorRequest $request): EvaluatorRequest
    {
        $request->permission_resource_scope_items = $this->_getPermissionResourceScopeItems($request);

        //TODO(demarco): if client enforcement is disabled we should only get all resources...

        /** @var Collection @permissions */
        $permissions = $request->client->all_permissions ? AuthorizationServerPolicy::permission()->all() : $request->client->permissions;
        /** @var Permission $permission */
        foreach ($this->_filterPermissions($request, $permissions->all()) as $permission) {
            $this->_evaluatePermission($permission, $request);
        }

        $request->resource_scope_results = $this->_getResouceScopeResults($request);

        return $request;
    }

    public function buildEvaluationAnalyse(EvaluatorRequest $request): EvaluationAnalyse
    {
        $analyse = new EvaluationAnalyse();

        /** @var KeyValuePair $permission_decision */
        foreach ($request->evaluator_results->permissions_decisions as $permission_decision) {
            /** @var Permission $permission_decision_key_typed */
            $permission_decision_key_typed = $permission_decision->key;
            /** @var PermissionDecision $permission_decision_value_typed */
            $permission_decision_value_typed = $permission_decision->value;

            $resources = $this->_getResources($request, $permission_decision_key_typed->permission);
            $scopes = $this->_getScopes($request, $permission_decision_key_typed->permission);

            $analyse_item = null;

            foreach ($resources as $resource) {
                $items_filtered = array_filter($analyse->items, fn (EvaluationAnalyseItem $p) => $p->resource_id == $resource->id);
                $analyse_item = count($items_filtered) > 0 ? (array_values($items_filtered)[0]) : null; //FirstOrDefault
                if ($analyse_item == null) {
                    $analyse_item = new EvaluationAnalyseItem($resource->id, $resource->name, $request->client->decision_strategy->name);
                    array_push($analyse->items, $analyse_item);
                }

                $evaluation = $this->buildEvaluation($request, $resource);
                /** @var EvaluationItem[] $result_resource */
                $result_resource = array_filter($evaluation->results, fn (EvaluationItem $p) => $p->rs_id == $resource->id);
                $result_scopes = count($result_resource) == 1 ? array_map(fn (EvaluationItem $p) => $p->scopes, $result_resource) : []; //SingleOrDefault
                foreach ($result_scopes as $result_scope) {
                    foreach ($result_scope as $scope) {
                        if (!in_array($scope, $analyse_item->scopes)) {
                            array_push($analyse_item->scopes, $scope);
                        }
                    }
                }
            }

            if ($analyse_item != null) {
                $evaluation_analyse_permission_item = new EvaluationAnalysePermissionItem(
                    $permission_decision_key_typed->id,
                    $permission_decision_key_typed->name,
                    $permission_decision_key_typed->decision_strategy->name,
                    $permission_decision_value_typed->result,
                    array_map(fn ($p) => $p->name, $scopes)
                );

                foreach ($permission_decision_value_typed->policies as $policy) {
                    /** @var Policy @$policy_key_typed */
                    $policy_key_typed = $policy->key;
                    /** @var bool $policy_value_typed */
                    $policy_value_typed = $policy->value;

                    $evaluation_analyse_permission_item->policies[] = new EvaluationAnalysePolicyItem(
                        $policy_key_typed->id,
                        $policy_key_typed->name,
                        $policy_value_typed
                    );
                }

                $analyse_item->permissions[] = $evaluation_analyse_permission_item;
            }
        }

        return $analyse;
    }

    public function buildEvaluation(EvaluatorRequest $request, Resource $filter_resouce = null): Evaluation
    {
        $result = new Evaluation();

        switch ($request->client->decision_strategy) {
            case DecisionStrategy::Affirmative:
                $resource_scope_results = array_filter($request->resource_scope_results, fn (ResourceScopeResult $p) => $p->granted === true && ($filter_resouce == null || $p->resource->id == $filter_resouce->id));
                $grouped_resource_scope_results = array_group($resource_scope_results, fn (ResourceScopeResult $p) => $p->resource, fn (Resource $p) => $p->id);
                foreach ($grouped_resource_scope_results as $value) {
                    $scope_names = array_map(fn (ResourceScopeResult $p) => $p->scope->name, $value->value);
                    $distinct_scopes = array_unique($scope_names);
                    $result->results[] = new EvaluationItem($value->key->id, $value->key->name, $distinct_scopes);
                }
                break;
            case DecisionStrategy::Unanimous:
                $resource_scope_results = array_filter($request->resource_scope_results, fn (ResourceScopeResult $p) => ($filter_resouce == null || $p->resource->id == $filter_resouce->id));
                $grouped_resource_scope_results = array_group($resource_scope_results, fn (ResourceScopeResult $p) => $p->resource, fn (Resource $p) => $p->id);
                foreach ($grouped_resource_scope_results as $value) {
                    $not_granted_items = array_filter($value->value, fn (ResourceScopeResult $p) => $p->granted === false);
                    $not_granted_scope_id = array_map(fn (ResourceScopeResult $p) => $p->scope->id, $not_granted_items);
                    $filter_scopes = array_filter($value->value, fn (ResourceScopeResult $p) => !in_array($p->scope->id, $not_granted_scope_id));
                    $scope_names = array_map(fn (ResourceScopeResult $p) => $p->scope->name, $filter_scopes);
                    $distinct_scopes = array_unique($scope_names);
                    $result->results[] = new EvaluationItem($value->key->id, $value->key->name, $distinct_scopes);
                }
                break;
            case DecisionStrategy::Consensus:
                throw new Error('NotImplementedException');
                break;
        }

        return $result;
    }

    private function _evaluatePermission(Permission $permission, EvaluatorRequest $request)
    {
        /** @var bool | null $permission_decision */
        $permission_decision = null;

        /** @var KeyValuePair[] $policy_result Policy/bool */
        $policy_result = [];

        $permission_pass = 0;
        $permission_refuse = 0;

        //TODO(demarco): THIS IS NOT CORRECT :::: We must try to respect this !!!!
        /*

            Policy Enforcement Mode
            Specifies how policies are enforced when processing authorization requests sent to the server.

                Enforcing
                (default mode) Requests are denied by default even when there is no policy associated with a given resource.

                Permissive
                Requests are allowed even when there is no policy associated with a given resource.

                Disabled
                Disables the evaluation of all policies and allows access to all resources.

            */

        foreach ($permission->policies as $policy) {
            if (!$request->cache->hasPolicyCache($policy)) {
                $policy_evaluated = false;

                switch ($request->client->policy_enforcement) {
                    case PolicyEnforcement::Enforcing:
                        $policy_evaluated = $policy->policy->evaluate($request);
                        break;
                    case PolicyEnforcement::Permissive:
                        $policy_evaluated = true;
                        break;
                }

                $request->cache->addPolicyCache($policy, $policy_evaluated);
            }
            $evaluator = $request->cache->getPolicyCacheWithoutNullable($policy);
            $policy_result[] = new KeyValuePair($policy, $evaluator);

            if ($evaluator) {
                ++$permission_pass;
            } else {
                ++$permission_refuse;
            }
        }

        switch ($permission->decision_strategy) {
            case DecisionStrategy::Affirmative:
                $permission_decision = $permission_pass > 0;
                break;
            case DecisionStrategy::Consensus:
                $permission_decision = ($permission_pass - $permission_refuse) > 0;
                break;
            case DecisionStrategy::Unanimous:
                $permission_decision = $permission_pass > 0 && $permission_refuse == 0;
                break;
        }

        $request->evaluator_results->addPermission($permission, new PermissionDecision($permission_decision, $policy_result));
    }

    private function _getResourceNameFromPermission(ScopePermission | ResourcePermission $permission): string | null
    {
        if ($permission instanceof ResourcePermission) {
            return $permission->resource != null ? $permission->resource->name : null;
        } else if ($permission instanceof ScopePermission) {
            return $permission->resource != null ? $permission->resource->name : null;
        }
        return null;
    }

    private function _getPermissionResourceScopeItems(EvaluatorRequest $request)
    {
        $result = [];

        if ($request->permissions != null) {
            /** @var string $permission */
            foreach ($request->permissions as $permission) {
                $split = explode($request->client->permission_splitter, $permission);
                if (count($split) == 2) {
                    $result[] = new PermissionResourceScopeItem($split[0], $split[1]);
                } else if (count($split) == 1) {
                    $result[] = new PermissionResourceScopeItem($split[0]);
                } else {
                    throw new Error('ArgumentOutOfRangeException: Permissions');
                }
            }
        }

        return $result;
    }

    /** @var Permission[] @permissions */
    private function _filterPermissions(EvaluatorRequest $request, array $permissions)
    {
        if(array_any($request->permission_resource_scope_items, fn (PermissionResourceScopeItem $p) => $p->resource_name == null)){
            return $permissions;
        }

        $all_valid_resource_names = array_filter($request->permission_resource_scope_items, fn (PermissionResourceScopeItem $p) => $p->resource_name != null);
        if (count($all_valid_resource_names) == 0) {
            return $permissions;
        }

        $all_accepted_resource_names = array_map(fn (PermissionResourceScopeItem $p) => $p->resource_name, $all_valid_resource_names);
        $result = array_filter($permissions, fn (Permission $p) => count(array_filter($all_accepted_resource_names, fn (string $a) => $a == $this->_getResourceNameFromPermission($p->permission))));

        return $result;
    }

    /** @var Scope[] $scopes */
    private function _filterScopes(EvaluatorRequest $request, Resource $resource, array $scopes)
    {
        if (count(array_filter($request->permission_resource_scope_items, fn (PermissionResourceScopeItem $p) => $p->resource_name == $resource->name)) > 0) {
            return $scopes;
        }

        $all_valid_resource_names = array_filter($request->permission_resource_scope_items, fn ($p) => ($p->resource_name == null || $p->resource_name == $resource->name) && $p->scope_name != null);
        if (count($all_valid_resource_names) == 0) {
            return $scopes;
        }

        $all_accepted_resource_names = array_map(fn (PermissionResourceScopeItem $p) => $p->scope_name, $all_valid_resource_names);
        $result = array_filter($scopes, fn (Scope $p) => count(array_filter($all_accepted_resource_names, fn (string $a) => $a == $p->scope_name)));

        return $result;
    }

    private function _getResouceScopeResults(EvaluatorRequest $request)
    {
        $resource_scope_results = [];

        foreach ($request->evaluator_results->permissions_decisions as $permissions_decision) {
            $resources = $this->_getResources($request, $permissions_decision->key->permission);
            $scopes = $this->_getScopes($request, $permissions_decision->key->permission);

            foreach ($resources as $resource) {
                foreach ($this->_filterScopes($request, $resource, $scopes) as $scope) {
                    $hash = ResourceScopeResult::getHash($permissions_decision->key, $resource, $scope);
                    if (!array_key_exists($hash, $resource_scope_results)) {
                        $resource_scope_results[$hash] = new ResourceScopeResult($permissions_decision->key, $resource, $scope);
                    }

                    /** @var ResourceScopeResult $item */
                    $item = $resource_scope_results[$hash];
                    if ($permissions_decision->value->result) {
                        ++$item->granted_count;
                    } else {
                        ++$item->denied_count;
                    }
                }
            }
        }

        /** @var ResourceScopeResult $scope_result */
        foreach ($resource_scope_results as $scope_result) {
            switch ($request->client->decision_strategy) {
                case DecisionStrategy::Affirmative:
                    $scope_result->granted = $scope_result->granted_count > 0;
                    break;
                case DecisionStrategy::Unanimous:
                    $scope_result->granted = $scope_result->denied_count == 0;
                    break;
                case DecisionStrategy::Consensus:
                    //NOTE(demarco): are you sure of that statment ?? NOT PRESENT IN original design....
                    $scope_result->granted = ($scope_result->granted_count - $scope_result->denied_count) > 0;
                    break;
            }
        }

        $result = array_values($resource_scope_results);
        return $result;
    }

    private function _getResources(EvaluatorRequest $request, ResourcePermission | ScopePermission $permission)
    {
        /** @var Resource[] */
        $result = [];

        /** @var Resource[] */
        $all_client_resources = $request->client->all_resources ? AuthorizationServerPolicy::resource()->all()->all() : $request->client->resources;

        if ($permission instanceof ResourcePermission) {
            if ($permission->resource != null) {
                $result[] = $permission->resource;
            } else {
                $regex = wildcardToRegex($permission->resource_type);
                if (!isNullOrEmptyString($regex)) {
                    $resources_to_add =  array_filter($all_client_resources, fn (Resource $p) => preg_match($regex, $p->type));
                    array_push($result, ...$resources_to_add);
                }
            }
        } else if ($permission instanceof ScopePermission) {
            if ($permission->resource != null) {
                $result[] = $permission->resource;
            }
        }

        if (count($result) == 0) {
            //TODO(demarco): add an empty resouce that reprents no resources....
        }

        return $result;
    }

    private function _getScopes(EvaluatorRequest $request, ResourcePermission | ScopePermission $permission)
    {
        /** @var Scope[] */
        $result = [];

        /** @var Resource[] */
        $all_client_resources = $request->client->all_resources ? AuthorizationServerPolicy::resource()->all()->all() : $request->client->resources;

        //TODO(demarco): We missing the all_scopes and the selected scope in the client...

        if ($permission instanceof ResourcePermission) {
            if ($permission->resource != null) {
                array_push($result, ...$permission->resource->scopes);
            } else {
                $regex = wildcardToRegex($permission->resource_type);
                if (!isNullOrEmptyString($regex)) {
                    $compatible_resources =  array_filter($all_client_resources, fn (Resource $p) => preg_match($regex, $p->type));
                    $scopes_to_add = array_map(fn (Resource $p) => $p->scopes()->get()->all(), $compatible_resources);
                    $scopes_to_add = array_flatten($scopes_to_add);
                    $scopes_to_add = array_distinct($scopes_to_add, fn (Scope $p) => $p->id);
                    array_push($result, ...$scopes_to_add);
                }
            }
        } else if ($permission instanceof ScopePermission) {
            if ($permission->resource != null) {
                array_push($result, ...$permission->scopes);
            }
        }

        return $result;
    }
}
