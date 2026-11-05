<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\StoreUserRequest;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(
        private UserService $users,
        private AuthService $auth,
    ) {}

    /**
     * Registra um novo usuário e retorna o token de acesso.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        ['user' => $user, 'token' => $token] = $this->auth->register($request->validated());

        return response()->json([
            'user' => UserResource::make($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Autentica o usuário e retorna um novo token de acesso.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        ['user' => $user, 'token' => $token] = $this->auth->login(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
        );

        return response()->json([
            'user' => UserResource::make($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Exibe o usuário autenticado.
     */
    public function show(Request $request): UserResource
    {
        /** @var User $user */
        $user = $request->user();

        return new UserResource($user);
    }

    /**
     * Atualiza o usuário autenticado.
     */
    public function update(UpdateUserRequest $request): UserResource
    {
        /** @var User $user */
        $user = $request->user();

        return new UserResource($this->users->update($user, $request->validated()));
    }

    /**
     * Remove o usuário autenticado e revoga seus tokens.
     */
    public function destroy(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $this->users->delete($user);

        return response()->noContent();
    }
}
