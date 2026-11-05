<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    public function __construct(private UserRepositoryInterface $users) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        return $this->users->update($user, $data);
    }

    /**
     * Revoga os tokens de acesso antes de remover o usuário, para não
     * deixar registros órfãos em `personal_access_tokens`.
     */
    public function delete(User $user): bool
    {
        $user->tokens()->delete();

        return $this->users->delete($user);
    }
}
