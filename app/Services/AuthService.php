<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Nome atribuído aos tokens de acesso emitidos para a API.
     */
    private const TOKEN_NAME = 'api';

    public function __construct(private UserRepositoryInterface $users) {}

    /**
     * Cria o usuário e emite o primeiro token de acesso.
     *
     * @param  array<string, mixed>  $data
     * @return array{user: User, token: string}
     */
    public function register(array $data): array
    {
        $user = $this->users->create($data);

        return [
            'user' => $user,
            'token' => $this->issueToken($user),
        ];
    }

    /**
     * Valida as credenciais e emite um novo token de acesso.
     *
     * @return array{user: User, token: string}
     *
     * @throws ValidationException Quando e-mail ou senha não conferem.
     */
    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return [
            'user' => $user,
            'token' => $this->issueToken($user),
        ];
    }

    private function issueToken(User $user): string
    {
        return $user->createToken(self::TOKEN_NAME)->plainTextToken;
    }
}
