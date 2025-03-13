<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use App\Databases\Contracts\UsuarioSetorContract;
use App\Databases\Repositories\UsuarioSetorRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        app()->bind(UsuarioSetorContract::class, UsuarioSetorRepository::class);
        Vite::prefetch(concurrency: 3);
    }
}
