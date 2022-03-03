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
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\ResourcePermission;
use Darkink\AuthorizationServer\Models\Scope;
use Darkink\AuthorizationServer\Models\ScopePermission;
use Darkink\AuthorizationServer\Services\IEvaluatorService;
use Error;
use Illuminate\Support\Collection;

class DefaultEvaluatorService implements IEvaluatorService
{

    public function Evaluate(EvaluatorRequest $request): EvaluatorRequest
    {
        $request->permission_resource_scope_items = $this->_getPermissionResourceScopeItems($request);

        /** @var Permission $permission */
        foreach ($this->_filterPermissions($request, $request->client->permissions) as $permission) {
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
            $resources = $this->_getResources($request, $permission_decision->key);
            $scopes = $this->_getScopes($request, $permission_decision->key);

            $analyse_item = null;

            foreach ($resources as $resource) {
                $items_filtered = array_filter($analyse->items, fn ($p) => $p->resource_id == $resource->id);
                $analyse_item = count($items_filtered) != 0 ? $items_filtered[0] : null;
                if ($analyse_item == null) {
                    $analyse_item = new EvaluationAnalyseItem($resource->id, $resource->name, $request->client->decisionStrategy->name);
                    $analyse->items[] = $analyse_item;
                }

                $evaluation = $this->buildEvaluation($request, $resource);
                $result_resource = array_filter($evaluation->results, fn ($p) => $p->rs_id == $resource->id);
                $result_scopes = count($result_resource) == 1 ? $result_resource->scopes : [];
                foreach ($result_scopes as $scope) {
                    $analyse_item->scopes[] = $scope;
                }
            }

            if ($analyse_item != null) {
                $evaluation_analyse_permission_item = new EvaluationAnalysePermissionItem(
                    $permission_decision->key->id,
                    $permission_decision->key->name,
                    $permission_decision->value->result,
                    $permission_decision->key->decision_strategy->name,
                    array_map(fn ($p) => $p->name, $scopes)
                );

                foreach ($permission_decision->value->policies as $policy => $policyGranted) {
                    $evaluation_analyse_permission_item->policies[] = new EvaluationAnalysePolicyItem(
                        $policy->id,
                        $policy->name,
                        $policyGranted
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

        switch ($request->client->decisionStrategy) {
            case DecisionStrategy::Affirmative:
                $resource_scope_results = array_filter($request->resource_scope_results, fn ($p) => $p->granted === true && ($filter_resouce == null || $p->resource->Id == $filter_resouce->id));
                $grouped_resource_scope_results = array_group($resource_scope_results, fn ($p) => $p->resource, fn ($p) => $p->id);
                foreach ($grouped_resource_scope_results as $value) {
                    $scope_names = array_map(fn ($p) => $p->scope->name, $value->value);
                    $distinct_scopes = array_unique($scope_names);
                    $result->results[] = new EvaluationItem($value->key->id, $value->key->name, $distinct_scopes);
                }
                break;
            case DecisionStrategy::Unanimous:
                $resource_scope_results = array_filter($request->resource_scope_results, fn ($p) => ($filter_resouce == null || $p->resource->Id == $filter_resouce->id));
                $grouped_resource_scope_results = array_group($resource_scope_results, fn ($p) => $p->resource, fn ($p) => $p->id);
                foreach ($grouped_resource_scope_results as $value) {
                    $not_granted_items = array_filter($value->value, fn ($p) => $p->granted === false);
                    $not_granted_scope_id = array_map(fn ($p) => $p->scope->id, $not_granted_items);
                    $filter_scopes = array_filter($value->value, fn ($p) => !in_array($p->scope->id, $not_granted_scope_id));
                    $scope_names = array_map(fn ($p) => $p->scope->name, $filter_scopes);
                    $distinct_scopes = array_unique($scope_names);
                    $result->results[] = new EvaluationItem($value->key->id, $value->key->name, $distinct_scopes);
                }
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

        foreach ($permission->policies as $policy) {
            if (!$request->cache->hasPolicyCache($policy)) {
                $request->cache->addPolicyCache($policy, $policy->evaluate($request));
            }
            $evaluator = $request->cache->getPolicyCacheWithoutNullable($policy);
            $policy_result[] = new KeyValuePair($policy, $evaluator);

            if ($evaluator) {
                ++$permission_pass;
            } else {
                ++$permission_refuse;
            }

            switch ($permission->decision_strategy) {
                case DecisionStrategy::Affirmative:
                    $permission_decision = $permission_pass > 0;
                    break;
                case DecisionStrategy::Consensus:
                    $permission_decision = ($permission_pass - $permission_refuse) > 0;
                    break;
                case DecisionStrategy::Unanimous:
                    $permission_decision = $permission_refuse == 0;
                    break;
            }

            $request->evaluator_results->addPermission($permission, new PermissionDecision($permission_decision, $policy_result));
        }
    }

    private function _getPermissionResourceScopeItems(EvaluatorRequest $request)
    {
        $result = [];

        if ($request->permissions != null) {
            /** @var string $permission */
            foreach ($request->permissions as $permission) {
                $split = explode($request->client->options->permissionSplitter, $permission);
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

    /** @param Permission[] $permissions */
    private function _filterPermissions(EvaluatorRequest $request, array | Collection $permissions)
    {
        if (count(array_filter($request->permission_resource_scope_items, fn (PermissionResourceScopeItem $p) => $p->resourceName == null)) > 0) {
            return $permissions;
        }

        $all_valid_resourceNames = array_filter($request->permission_resource_scope_items, fn (PermissionResourceScopeItem $p) => $p->resourceName != null);
        if (count($all_valid_resourceNames) == 0) {
            return $permissions;
        }

        $all_accepted_resourceNames = array_map(fn (PermissionResourceScopeItem $p) => $p->resourceName, $all_valid_resourceNames);
        $result = array_filter($permissions, fn ($p) => count(array_filter($all_accepted_resourceNames, fn (PermissionResourceScopeItem $a) => $a->resourceName == $p->resourceName)));

        return $result;
    }

    /** @param Scope[] $scopes */
    private function _filterScopes(EvaluatorRequest $request, Resource $resource, array | Collection $scopes)
    {
        if (count(array_filter($request->permission_resource_scope_items, fn (PermissionResourceScopeItem $p) => $p->resourceName == $resource->name)) > 0) {
            return $scopes;
        }

        $all_valid_resourceNames = array_filter($request->permission_resource_scope_items, fn ($p) => ($p->resourceName == null || $p->resourceName == $resource->name) && $p->ScopeName != null);
        if (count($all_valid_resourceNames) == 0) {
            return $scopes;
        }

        $all_accepted_resourceNames = array_map(fn (PermissionResourceScopeItem $p) => $p->scopeName, $all_valid_resourceNames);
        $result = array_filter($scopes, fn ($p) => count(array_filter($all_accepted_resourceNames, fn (PermissionResourceScopeItem $a) => $a->scopeName == $p->scopeName)));

        return $result;
    }

    private function _getResouceScopeResults(EvaluatorRequest $request)
    {
        $resource_scope_results = [];

        foreach ($request->evaluator_results->permissions_decisions as $permissions_decision) {
            $resources = $this->_getResources($request, $permissions_decision->key);
            $scopes = $this->_getScopes($request, $permissions_decision->key);

            foreach ($resources as $resource) {
                foreach ($this->_filterScopes($request, $resource, $scopes) as $scope) {
                    $hash = ResourceScopeResult::getHash($permissions_decision->key, $resource, $scope);
                    if (!array_key_exists($hash, $resource_scope_results)) {
                        $resource_scope_results[$hash] = new ResourceScopeResult($permissions_decision->key, $resource, $scope);
                    }

                    /** @var ResourceScopeResult $item */
                    $item = $resource[$hash];
                    if ($permissions_decision->value->result) {
                        ++$item->granted_count;
                    } else {
                        ++$item->DeniedCount;
                    }
                }
            }
        }

        foreach ($resource_scope_results as $hash => $scope_result) {
            switch ($request->client->options->decision_strategy) {
                case DecisionStrategy::Affirmative:
                    //NOTE(demarco): are you sure of that statment ??
                    $scope_result->granted = $scope_result->granted_count == 0;
                    break;
                case DecisionStrategy::Unanimous:
                    //NOTE(demarco): are you sure of that statment ??
                    $scope_result->granted = $scope_result->denied_count == 0;
                    break;
            }
        }

        $result = array_values($resource_scope_results);
        return $result;
    }

    private function _getResources(EvaluatorRequest $request, Permission $permission)
    {
        /** @var Resource[] */
        $result = [];

        /** @var Resource[] */
        $all_client_resources = $request->client->resources()->all();

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

    private function _getScopes(EvaluatorRequest $request, Permission $permission)
    {
        /** @var Scope[] */
        $result = [];

        /** @var Resource[] */
        $all_client_resources = $request->client->resources()->with('resources')->all();

        if ($permission instanceof ResourcePermission) {
            if ($permission->resource != null) {
                $request[] = $permission->resource;
            } else {
                $regex = wildcardToRegex($permission->resource_type);
                if (!isNullOrEmptyString($regex)) {
                    $compatible_resources =  array_filter($all_client_resources, fn (Resource $p) => preg_match($regex, $p->type));
                    $scopes_to_add = array_map(fn (Resource $p) => $p->scopes, $compatible_resources);
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
