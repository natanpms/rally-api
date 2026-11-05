<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Limite geral de requisições da API. Os limiters nomeados (`api`,
        // `login`, `register`) são definidos em `AppServiceProvider::boot()`.
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        /*
         * Os callbacks abaixo padronizam as respostas de erro da API no formato
         * `{"message": "..."}` com texto em português. São avaliados na ordem de
         * registro, por isso o fallback genérico fica por último.
         *
         * `ValidationException` não é tratada aqui: o Laravel já responde 422 com
         * `{"message": ..., "errors": {...}}` e as mensagens vêm traduzidas de
         * `lang/pt_BR/validation.php`.
         */

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json(['message' => __('http.unauthenticated')], 401);
        });

        $exceptions->render(function (AuthorizationException|AccessDeniedHttpException $e, Request $request) {
            return response()->json(['message' => __('http.forbidden')], 403);
        });

        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e, Request $request) {
            return response()->json(['message' => __('http.not_found')], 404);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            return response()->json(['message' => __('http.method_not_allowed')], 405);
        });

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            $headers = $e->getHeaders();

            return response()->json([
                'message' => __('http.too_many_requests', ['seconds' => $headers['Retry-After'] ?? 60]),
            ], 429, $headers);
        });

        // Fallback: erros não previstos viram 500 com mensagem genérica. Em modo
        // debug devolve `null` para preservar o trace completo do Laravel.
        $exceptions->render(function (Throwable $e, Request $request) {
            if (config('app.debug') || $e instanceof HttpExceptionInterface) {
                return null;
            }

            return response()->json(['message' => __('http.server_error')], 500);
        });
    })->create();
