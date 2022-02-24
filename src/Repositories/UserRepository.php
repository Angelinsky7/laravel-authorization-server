<?php

namespace Darkink\AuthorizationServer\Repositories;

use Illuminate\Foundation\Auth\User;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function find(int $id): User
    {
        $user = Policy::user();
        return $user->where($user->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::user();
    }

    protected function resolve(mixed $roles, mixed $memberofs)
    {

        if (count($roles) != 0 && !is_object($roles[0])) {
            $roles = Policy::role()->all()->whereIn(Policy::role()->getKeyName(), $roles);
        }

        if (count($memberofs) != 0 && !is_object($memberofs[0])) {
            $memberofsWithoutPrefix = array_map(fn ($p) => substr($p, strlen('g')), $memberofs);
            $memberofs = Policy::group()->all()->whereIn(Policy::group()->getKeyName(), $memberofsWithoutPrefix);
        }

        return [
            'roles' => $roles,
            'memberofs' => $memberofs,
        ];
    }

    public function create(string $name, string $email, string | null $password, array $roles, array $memberofs): User
    {
        DB::beginTransaction();

        try {

            extract($this->resolve($roles, $memberofs));

            $user = Policy::user();
            $user->name = $name;
            $user->email = $email;

            if ($password != null && $password != '') {
                $user->password = Hash::make($password);
            }

            $user->remember_token = Str::random(10);

            $user->save();

            $user->roles()->saveMany($roles);
            $user->memberofs()->saveMany($memberofs);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $user;
    }

    public function update(User $user, string $name, string $email, string | null $password, array $roles, array $memberofs): User
    {
        DB::beginTransaction();

        try {

            extract($this->resolve($roles, $memberofs));

            $user->name = $name;
            $user->email = $email;

            if ($password != null && $password != '') {
                $user->password = Hash::make($password);
            }

            $user->save();

            /** @var \Illuminate\Support\Collection $roles */
            $user->roles()->sync(is_array($roles) ? $roles : $roles->map(fn ($p) => $p->id)->toArray());
            /** @var \Illuminate\Support\Collection $memberofs */
            $user->memberofs()->sync(is_array($memberofs) ? $memberofs : $memberofs->map(fn ($p) => $p->id)->toArray());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $user;
    }

    public function delete(User $user)
    {
        $user->delete();
    }
}
