<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Client;
use Darkink\AuthorizationServer\Models\DecisionStrategy;
use Darkink\AuthorizationServer\Models\PolicyEnforcement;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientRepository
{
    public function find(string | int $id): Client
    {
        $client = Policy::oauthClient();
        return $client->where($client->getKeyName(), $id)->first()->client;
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

    // protected function resolve(mixed $roles, mixed $memberofs)
    // {

    //     if (count($roles) != 0 && !is_object($roles[0])) {
    //         $roles = Policy::role()->all()->whereIn(Policy::role()->getKeyName(), $roles);
    //     }

    //     if (count($memberofs) != 0 && !is_object($memberofs[0])) {
    //         $memberofsWithoutPrefix = array_map(fn ($p) => substr($p, strlen('g')), $memberofs);
    //         $memberofs = Policy::group()->all()->whereIn(Policy::group()->getKeyName(), $memberofsWithoutPrefix);
    //     }

    //     return [
    //         'roles' => $roles,
    //         'memberofs' => $memberofs,
    //     ];
    // }




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
        string $client_id,
        bool $require_client_secret,
        string $client_name,
        string $description,
        string $client_uri,
        PolicyEnforcement | int $policy_enforcement,
        DecisionStrategy | int $decision_strategy,
        bool $analyse_mode_enabled,
    ): Client {
        DB::beginTransaction();

        try {

            // extract($this->resolve($roles, $memberofs));

            $oauth = Policy::oauthClient()->forceFill([
                'name' => $name,
                'user_id' => $user_id,
                'secret' => Hash::make($secret),
                'provider' => $provider,
                'redirect' => $redirect,
                'personal_access_client' => $personal_access_client,
                'password_client' => $password_client,
                'revoked' => $revoked,
            ]);
            $oauth->save();

            $client = Policy::client()->forceFill([
                'oauth_id' => $oauth->id,
                'enabled' => $enabled,
                'client_id' => $client_id,
                'require_client_secret' => $require_client_secret,
                'client_name' => $client_name,
                'description' => $description,
                'client_uri' => $client_uri,
                'policy_enforcement' => $policy_enforcement,
                'decision_strategy' => $decision_strategy,
                'analyse_mode_enabled' => $analyse_mode_enabled
            ]);
            $client->save();
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
        string $client_id,
        bool $require_client_secret,
        string $client_name,
        string $description,
        string $client_uri,
        PolicyEnforcement | int $policy_enforcement,
        DecisionStrategy | int $decision_strategy,
        bool $analyse_mode_enabled,
    ): Client {
        DB::beginTransaction();

        try {

            // extract($this->resolve($roles, $memberofs));

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
                $oauth->secret = Hash::make($secret);
            }
            $oauth->save();

            $client->forceFill([
                'enabled' => $enabled,
                // 'client_id' => $client_id,
                'require_client_secret' => $require_client_secret,
                'client_name' => $client_name,
                'description' => $description,
                'client_uri' => $client_uri,
                'policy_enforcement' => $policy_enforcement,
                'decision_strategy' => $decision_strategy,
                'analyse_mode_enabled' => $analyse_mode_enabled
            ]);
            $client->save();

            // /** @var \Illuminate\Support\Collection $roles */
            // $client->roles()->sync(is_array($roles) ? $roles : $roles->map(fn ($p) => $p->id)->toArray());
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
