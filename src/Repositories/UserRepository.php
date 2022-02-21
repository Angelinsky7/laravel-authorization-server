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

    public function create(string $name, string $email, string | null $password): User
    {
        DB::beginTransaction();

        try {
            $user = Policy::user();
            $user->name = $name;
            $user->email = $email;

            if ($password != null && $password != '') {
                $user->password = Hash::make($password);
            }

            $user->remember_token = Str::random(10);

            $user->save();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $user;
    }

    public function update(User $user, string $name, string $email, string | null $password): User
    {
        DB::beginTransaction();

        try {
            $user->name = $name;
            $user->email = $email;

            if ($password != null && $password != '') {
                $user->password = Hash::make($password);
            }

            $user->save();
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
