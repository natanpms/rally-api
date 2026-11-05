<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\StoreUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @tags Autenticação
 */
class AuthController extends Controller
{
    public function __construct(private AuthService $auth) {}

    /**
     * Registra um novo usuário e retorna o token de acesso.
     */
    public function register(StoreUserRequest $request): JsonResponse
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
     * Revoga os tokens de acesso do usuário autenticado.
     */
    public function logout(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $this->auth->logout($user);

        return response()->noContent();
    }
}
