<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Client;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\PolicyEnforcement;
use Darkink\AuthorizationServer\Policy;
use Error;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientRepository
{
    public function find(string | int $id): Client
    {
        $client = Policy::oauthClient();
        $result = $client->where($client->getKeyName(), $id)->first();
        if ($result == null) {
            throw new Error('Client not found');
        }
        return $result->client;
    }

    // public function find_by_client_id(string $id): Client
    // {
    //     $client = Policy::oauthClient();
    //     return $client->where('client_id', $id)->first();
    // }

    public function gets()
    {
        return Policy::oauthClient()->with('client');
    }

    protected function resolve(mixed $permissions)
    {

        if (count($permissions) != 0 && !is_object($permissions[0])) {
            $permissions = Policy::permission()->all()->whereIn(Policy::permission()->getKeyName(), $permissions);
        }

        return [
            'permissions' => $permissions,
        ];
    }

    public function create(
        string $name,
        int | string | null $user_id,
        string | null $secret,
        string | null $provider,
        string $redirect,
        bool $personal_access_client,
        bool $password_client,
        bool $revoked,
        bool $enabled,
        // string $client_id,
        bool $require_client_secret,
        // string $client_name,
        string $description,
        string $client_uri,
        PolicyEnforcement | int $policy_enforcement,
        DecisionStrategy | int $decision_strategy,
        string $permission_splitter,
        bool $analyse_mode_enabled,
        bool $json_mode_enabled,
        bool $all_resources,
        bool $all_scopes,
        // bool $all_roles,
        // bool $all_groups,
        // bool $all_policies,
        bool $all_permissions,
        mixed $permissions
    ): Client {
        DB::beginTransaction();

        try {

            extract($this->resolve($permissions));

            $oauth = Policy::oauthClient()->forceFill([
                'name' => $name,
                'user_id' => $user_id,
                // 'secret' => Hash::make($secret),
                'provider' => $provider,
                'redirect' => $redirect,
                'personal_access_client' => $personal_access_client,
                'password_client' => $password_client,
                'revoked' => $revoked,
            ]);
            $oauth->setSecretAttribute($secret);
            $oauth->save();

            $client = Policy::client()->forceFill([
                'oauth_id' => $oauth->id,
                'enabled' => $enabled,
                // 'client_id' => $client_id,
                'require_client_secret' => $require_client_secret,
                // 'client_name' => $client_name,
                'description' => $description,
                'client_uri' => $client_uri,
                'policy_enforcement' => $policy_enforcement,
                'decision_strategy' => $decision_strategy,
                'permission_splitter' => $permission_splitter,
                'analyse_mode_enabled' => $analyse_mode_enabled,
                'json_mode_enabled' => $json_mode_enabled,
                'all_resources' => $all_resources,
                'all_scopes' => $all_scopes,
                // 'all_roles' => $all_roles,
                // 'all_groups' => $all_groups,
                // 'all_policies' => $all_policies,
                'all_permissions' => $all_permissions
            ]);
            $client->save();

            if (!$all_permissions) {
                $client->permissions()->saveMany($permissions);
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $client;
    }

    public function update(
        Client $client,
        string $name,
        int | string | null $user_id,
        string | null $secret,
        string | null $provider,
        string $redirect,
        bool $personal_access_client,
        bool $password_client,
        bool $revoked,
        bool $enabled,
        // string $client_id,
        bool $require_client_secret,
        // string $client_name,
        string $description,
        string $client_uri,
        PolicyEnforcement | int $policy_enforcement,
        DecisionStrategy | int $decision_strategy,
        string $permission_splitter,
        bool $analyse_mode_enabled,
        bool $json_mode_enabled,
        bool $all_resources,
        bool $all_scopes,
        // bool $all_roles,
        // bool $all_groups,
        // bool $all_policies,
        bool $all_permissions,
        mixed $permissions
    ): Client {
        DB::beginTransaction();

        try {

            extract($this->resolve($permissions));

            $oauth = Policy::oauthClient()->find($client->oauth_id);

            $oauth->forceFill([
                'name' => $name,
                'user_id' => $user_id,
                'provider' => $provider,
                'redirect' => $redirect,
                'personal_access_client' => $personal_access_client,
                'password_client' => $password_client,
                'revoked' => $revoked,
            ]);
            if ($secret != null) {
                // $oauth->secret = Hash::make($secret);
                $oauth->setSecretAttribute($secret);
            }
            $oauth->save();

            $client->forceFill([
                'enabled' => $enabled,
                // 'client_id' => $client_id,
                'require_client_secret' => $require_client_secret,
                // 'client_name' => $client_name,
                'description' => $description,
                'client_uri' => $client_uri,
                'policy_enforcement' => $policy_enforcement,
                'decision_strategy' => $decision_strategy,
                'permission_splitter' => $permission_splitter,
                'analyse_mode_enabled' => $analyse_mode_enabled,
                'json_mode_enabled' => $json_mode_enabled,
                'all_resources' => $all_resources,
                'all_scopes' => $all_scopes,
                // 'all_roles' => $all_roles,
                // 'all_groups' => $all_groups,
                // 'all_policies' => $all_policies,
                'all_permissions' => $all_permissions
            ]);
            $client->save();

            if ($all_permissions) {
                $client->permissions()->sync([]);
            } else {
                /** @var \Illuminate\Support\Collection $permissions */
                $client->permissions()->sync(is_array($permissions) ? $permissions : $permissions->map(fn ($p) => $p->id)->toArray());
            }
            // /** @var \Illuminate\Support\Collection $memberofs */
            // $client->memberofs()->sync(is_array($memberofs) ? $memberofs : $memberofs->map(fn ($p) => $p->id)->toArray());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $client;
    }

    public function delete(Client $client)
    {
        $oauth = Policy::oauthClient()->find($client->oauth_id);
        $oauth->delete();
    }
}
