<?php

namespace Darkink\AuthorizationServer\Services\_Default;

use Darkink\AuthorizationServer\Helpers\KeyValuePair;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\Evaluation;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluationAnalyse;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluationAnalyseItem;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluationAnalysePermissionItem;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluationAnalysePolicyItem;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluationItem;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\EvaluatorRequest;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\PermissionResourceScopeItem;
use Darkink\AuthorizationServer\Http\Requests\Evaluator\ResourceScopeResult;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Permission;
use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\ResourcePermission;
use Darkink\AuthorizationServer\Models\Scope;
use Darkink\AuthorizationServer\Models\ScopePermission;
use Error;
use phpDocumentor\Reflection\Types\Callable_;

class DefaultEvaluatorService
{

    public function Evaluate(EvaluatorRequest $request): EvaluatorRequest
    {
        $request->permissionResourceScopeItems = $this->_getPermissionResourceScopeItems($request);

        /** @var Permission $permission */
        foreach ($this->_filterPermissions($request, $request->client->permissions) as $permission) {
            $permission->evaluate($request);
        }

        $request->resourceScopeResults = $this->_getResouceScopeResults($request);

        return $request;
    }

    public function buildEvaluationAnalyse(EvaluatorRequest $request): EvaluationAnalyse
    {
        $analyse = new EvaluationAnalyse();

        foreach ($request->results->permission_decisions as $permission => $permission_decision) {
            $resources = $this->_getResources($request, $permission);
            $scopes = $this->_getScopes($request, $permission);

            $analyseItem = null;

            foreach ($resources as $resource) {
                $itemsFiltered = array_filter($analyse->items, fn ($p) => $p->resource_id == $resource->id);
                $analyseItem = count($itemsFiltered) != 0 ? $itemsFiltered[0] : null;
                if ($analyseItem == null) {
                    $analyseItem = new EvaluationAnalyseItem($resource->id, $resource->name, $request->client->decisionStrategy->name);
                    $analyse->items[] = $analyseItem;
                }

                $evaluation = $this->buildEvaluation($request, $resource);
                $resultResource = array_filter($evaluation->results, fn ($p) => $p->rs_id == $resource->id);
                $resultScopes = count($resultResource) == 1 ? $resultResource->scopes : [];
                foreach ($resultScopes as $scope) {
                    $analyseItem->scopes[] = $scope;
                }
            }

            if ($analyseItem != null) {
                $evaluationAnalysePermissionItem = new EvaluationAnalysePermissionItem(
                    $permission->id,
                    $permission->name,
                    $permission_decision->result,
                    $permission->decision_strategy->name,
                    array_map(fn ($p) => $p->name, $scopes)
                );

                foreach ($permission_decision->policies as $policy => $policyGranted) {
                    $evaluationAnalysePermissionItem->policies[] = new EvaluationAnalysePolicyItem(
                        $policy->id,
                        $policy->name,
                        $policyGranted
                    );
                }

                $analyseItem->permissions[] = $evaluationAnalysePermissionItem;
            }
        }

        return $analyse;
    }

    public function buildEvaluation(EvaluatorRequest $request, Resource $filterResouce = null): Evaluation
    {
        $result = new Evaluation();

        switch ($request->client->decisionStrategy) {
            case DecisionStrategy::Affirmative:
                $resourceScopeResults = array_filter($request->resourceScopeResults, fn ($p) => $p->granted === true && ($filterResouce == null || $p->resource->Id == $filterResouce->id));
                $groupedResourceScopeResults = $this->array_group($resourceScopeResults, fn ($p) => $p->resource, fn ($p) => $p->id);
                foreach ($groupedResourceScopeResults as $value) {
                    $scopeNames = array_map(fn ($p) => $p->scope->name, $value->value);
                    $distinctScopes = array_unique($scopeNames);
                    $result->results[] = new EvaluationItem($value->key->id, $value->key->name, $distinctScopes);
                }
                break;
            case DecisionStrategy::Unanimous:
                $resourceScopeResults = array_filter($request->resourceScopeResults, fn ($p) => ($filterResouce == null || $p->resource->Id == $filterResouce->id));
                $groupedResourceScopeResults = $this->array_group($resourceScopeResults, fn ($p) => $p->resource, fn ($p) => $p->id);
                foreach ($groupedResourceScopeResults as $value) {
                    $notGrantedItems = array_filter($value->value, fn ($p) => $p->granted === false);
                    $notGrantedScopeId = array_map(fn ($p) => $p->scope->id, $notGrantedItems);
                    $filterScopes = array_filter($value->value, fn ($p) => !in_array($p->scope->id, $notGrantedScopeId));
                    $scopeNames = array_map(fn ($p) => $p->scope->name, $filterScopes);
                    $distinctScopes = array_unique($scopeNames);
                    $result->results[] = new EvaluationItem($value->key->id, $value->key->name, $distinctScopes);
                }
                break;
        }

        return $result;
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
    private function _filterPermissions(EvaluatorRequest $request, array $permissions)
    {
        if (count(array_filter($request->permissionResourceScopeItems, fn (PermissionResourceScopeItem $p) => $p->resourceName == null)) > 0) {
            return $permissions;
        }

        $all_valid_resourceNames = array_filter($request->permissionResourceScopeItems, fn (PermissionResourceScopeItem $p) => $p->resourceName != null);
        if (count($all_valid_resourceNames) == 0) {
            return $permissions;
        }

        $all_accepted_resourceNames = array_map(fn (PermissionResourceScopeItem $p) => $p->resourceName, $all_valid_resourceNames);
        $result = array_filter($permissions, fn ($p) => count(array_filter($all_accepted_resourceNames, fn (PermissionResourceScopeItem $a) => $a->resourceName == $p->resourceName)));

        return $result;
    }

    /** @param Scope[] $scopes */
    private function _filterScopes(EvaluatorRequest $request, Resource $resource, array $scopes)
    {
        if (count(array_filter($request->permissionResourceScopeItems, fn (PermissionResourceScopeItem $p) => $p->resourceName == $resource->name)) > 0) {
            return $scopes;
        }

        $all_valid_resourceNames = array_filter($request->permissionResourceScopeItems, fn ($p) => ($p->resourceName == null || $p->resourceName == $resource->name) && $p->ScopeName != null);
        if (count($all_valid_resourceNames) == 0) {
            return $scopes;
        }

        $all_accepted_resourceNames = array_map(fn (PermissionResourceScopeItem $p) => $p->scopeName, $all_valid_resourceNames);
        $result = array_filter($scopes, fn ($p) => count(array_filter($all_accepted_resourceNames, fn (PermissionResourceScopeItem $a) => $a->scopeName == $p->scopeName)));

        return $result;
    }

    private function _getResouceScopeResults(EvaluatorRequest $request)
    {
        $resourceScopeResults = [];

        foreach ($request->evaluatorResults->permissionsDecisions as $permission => $permissionDecision) {
            $resources = $this->_getResources($request, $permission);
            $scopes = $this->_getScopes($request, $permission);

            foreach ($resources as $resource) {
                foreach ($this->_filterScopes($request, $resource, $scopes) as $scope) {
                    $hash = ResourceScopeResult::getHash($permission, $resource, $scope);
                    if (!array_key_exists($hash, $resourceScopeResults)) {
                        $resourceScopeResults[$hash] = new ResourceScopeResult($permission, $resource, $scope);
                    }

                    /** @var ResourceScopeResult $item */
                    $item = $resource[$hash];
                    if ($permissionDecision->result) {
                        ++$item->grantedCount;
                    } else {
                        ++$item->DeniedCount;
                    }
                }
            }
        }

        foreach ($resourceScopeResults as $hash => $scopeResult) {
            switch ($request->client->options->decision_strategy) {
                case DecisionStrategy::Affirmative:
                    //NOTE(demarco): are you sure of that statment ??
                    $scopeResult->granted = $scopeResult->GrantedCount == 0;
                    break;
                case DecisionStrategy::Unanimous:
                    //NOTE(demarco): are you sure of that statment ??
                    $scopeResult->granted = $scopeResult->DeniedCount == 0;
                    break;
            }
        }

        $result = array_values($resourceScopeResults);
        return $result;
    }

    private function _wildcardToRegex(string $src): string
    {
        $pattern = $src;
        $pattern = str_replace('\*', '.*', $pattern);
        $pattern = str_replace('\?', '.', $pattern);

        return "^/${pattern}$/i";
    }

    private function _isNullOrEmptyString($str)
    {
        return ($str === null || trim($str) === '');
    }

    private function array_distinct(array $src, callable $callable): array
    {
        $result = array_map($callable, $src);
        $unique = array_unique($result);
        return array_values(array_intersect_key($src, $unique));
    }

    /** @return KeyValuePair[] */
    private function array_group(array $items, callable $callable_group, callable $callable_group_key): array
    {
        $resultsByKey = [];
        $groups = [];

        foreach ($items as $item) {
            $group = $callable_group($item);
            $group_key = $callable_group_key($group);
            if (!array_key_exists($group_key, $groups)) {
                $groups[$group_key] = $group;
                $resultsByKey[$group_key] = [];
            }
            $resultsByKey[$group_key][] = $item;
        }

        $result = [];
        foreach ($groups as $key => $item) {
            $result[$key] = new KeyValuePair($item, $resultsByKey[$key]);
        }

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
                $regex = $this->_wildcardToRegex($permission->resource_type);
                if (!$this->_isNullOrEmptyString($regex)) {
                    $resourcesToAdd =  array_filter($all_client_resources, fn (Resource $p) => preg_match($regex, $p->type));
                    array_push($result, ...$resourcesToAdd);
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
                $regex = $this->_wildcardToRegex($permission->resource_type);
                if (!$this->_isNullOrEmptyString($regex)) {
                    $compatibleResources =  array_filter($all_client_resources, fn (Resource $p) => preg_match($regex, $p->type));
                    $scopesToAdd = array_map(fn (Resource $p) => $p->scopes, $compatibleResources);
                    $scopesToAdd = $this->array_distinct($scopesToAdd, fn (Scope $p) => $p->id);
                    array_push($result, ...$scopesToAdd);
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
