<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @tags Usuário
 */
class UserController extends Controller
{
    public function __construct(private UserService $users) {}

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
