<?php

namespace App\Providers;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Mapeia os contratos de repositório para  implementações.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Define os limites de requisição usados pelo middleware `throttle`.
     */
    private function configureRateLimiting(): void
    {
        // Limite geral aplicado a todas as rotas da API.
        RateLimiter::for('api', fn(Request $request) => Limit::perMinute(60)
            ->by($request->user()?->id ?: $request->ip()));

        // Login tem limite duplo: por credencial (evita força bruta em uma conta)
        // e por IP (evita varredura de várias contas a partir do mesmo cliente).
        RateLimiter::for('login', fn(Request $request) => [
            Limit::perMinute(5)->by(Str::lower((string) $request->input('email')) . '|' . $request->ip()),
            Limit::perMinute(20)->by($request->ip()),
        ]);

        RateLimiter::for('register', fn(Request $request) => Limit::perMinute(5)->by($request->ip()));
    }
}
