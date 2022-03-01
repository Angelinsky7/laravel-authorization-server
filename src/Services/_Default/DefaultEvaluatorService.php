<?php

namespace Darkink\AuthorizationServer\Services\_Default;

use Darkink\AuthorizationServer\Http\Requests\EvaluatorRequest;
use Darkink\AuthorizationServer\Http\Requests\EvaluatorResult;
use Darkink\AuthorizationServer\Http\Requests\EvaluatorRun;
use Darkink\AuthorizationServer\Http\Requests\PermissionResourceScopeItem;
use Darkink\AuthorizationServer\Http\Requests\ResourceScopeResult;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\Permission;
use Error;

class DefaultEvaluatorService
{

    //TODO(demarco): Please check if we really need a EvaluatorRequest AND a EvaluatorRun... old version use the same object

    public function Evaluate(EvaluatorRequest $request): EvaluatorResult
    {
        $run = new EvaluatorRun($request);

        $run->permissionResourceScopeItems = $this->_getPermissionResourceScopeItems($request);

        /** @var Permission $permission */
        foreach ($this->_filterPermissions($run, $request->client->permissions) as $permission) {
            $permission->evaluate($request);
        }

        $run->resourceScopeResults = $this->_getResouceScopeResults($run);

        return $run->result;
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

    private function _filterPermissions(EvaluatorRun $run, array $src)
    {
        if (count(array_filter($run->permissionResourceScopeItems, fn ($p) => $p->resourceName == null)) > 0) {
            return $src;
        }

        $all_valid_resourceNames = array_filter($run->permissionResourceScopeItems, fn ($p) => $p->resourceName != null);
        if (count($all_valid_resourceNames) == 0) {
            return $src;
        }

        $all_accepted_resourceNames = array_map(fn ($p) => $p->resourceName, $all_valid_resourceNames);
        $result = array_filter($src, fn ($p) => count(array_filter($all_accepted_resourceNames, fn ($a) => $a->resourceName == $p->resourceName)));

        return $result;
    }

    private function _getResouceScopeResults(EvaluatorRun $run)
    {
        $resourceScopeResults = [];

        foreach ($run->request->evaluatorResults->permissionsDecisions as $permission => $permissionDecision) {
            $resources = $this->_getResources($run, $permission);
            $scopes = $this->_getScopes($run, $permission);

            foreach ($resources as $resource) {
                foreach ($this->_filterScopes($run, $resource, $scopes) as $scope) {
                    $hash = $this->_generateHash($permission, $resource, $scope);
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
            switch ($run->request->client->options->decision_strategy) {
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

    private function _getResources(EvaluatorRun $run, Permission $permission) {
        // List<Resource> result = new List<Resource>();
        // if (permission is ResourcePermission resourcePermission) {
        //     if (resourcePermission.Resource != null) {
        //         result.Add(resourcePermission.Resource);
        //     } else {
        //         Regex regex = resourcePermission.ResouceType?.WildcardToRegex();
        //         if (regex != null) {
        //             result.AddRange(request.Client.Resources.Where(p => regex.IsMatch(p.Type)));
        //         }
        //     }
        // } else if (permission is ScopePermission scopePermission) {
        //     if (scopePermission.Resource != null) {
        //         result.Add(scopePermission.Resource);
        //     }
        // }

        // if (result.Count == 0) {
        //     //TODO(demarco): add an empty resouce that reprents no resources....
        //     if (true) { }
        // }

        // return Task.FromResult(result.AsEnumerable());
        return [];
    }

    private function _getScopes(EvaluatorRun $run, Permission $permission) {
        // List<Scope> result = new List<Scope>();
        // if (permission is ResourcePermission resourcePermission) {
        //     if (resourcePermission.Resource != null) {
        //         result.AddRange(resourcePermission.Resource.Scopes);
        //     } else {
        //         Regex regex = resourcePermission.ResouceType?.WildcardToRegex();
        //         if (regex != null) {
        //             result.AddRange(request.Client.Resources.Where(p => regex.IsMatch(p.Type)).SelectMany(p => p.Scopes).Distinct());
        //         }
        //     }
        // } else if (permission is ScopePermission scopePermission) {
        //     if (scopePermission.Resource != null) {
        //         result.AddRange(scopePermission.Scopes);
        //     }
        // }

        // return Task.FromResult(result.AsEnumerable());

        return [];
    }
}

}
