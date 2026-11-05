<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);

        return $user->refresh();
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }
}
