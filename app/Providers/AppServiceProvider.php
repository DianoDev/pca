<?php

namespace App\Providers;

use App\Databases\Contracts\PlanoContratacaoEntidadeContract;
use App\Databases\Contracts\CicloContratacaoContract;
use App\Databases\Repositories\CicloContratacaoRepository;
use App\Databases\Repositories\PlanoContratacaoEntidadeRepository;
use Illuminate\Support\Facades\Vite;
use App\Databases\Contracts\ItemProrrogacaoContract;
use App\Databases\Repositories\ItemProrrogacaoRepository;
use App\Databases\Contracts\ItemContratacaoContract;
use App\Databases\Repositories\ItemContratacaoRepository;
use App\Databases\Contracts\PlanoContratacaoSetorContract;
use App\Databases\Repositories\PlanoContratacaoSetorRepository;
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
        app()->bind(CicloContratacaoContract::class, CicloContratacaoRepository::class);
        app()->bind(ItemProrrogacaoContract::class, ItemProrrogacaoRepository::class);
        app()->bind(ItemContratacaoContract::class, ItemContratacaoRepository::class);
        app()->bind(PlanoContratacaoSetorContract::class, PlanoContratacaoSetorRepository::class);
        app()->bind(PlanoContratacaoEntidadeContract::class, PlanoContratacaoEntidadeRepository::class);
        app()->bind(UsuarioSetorContract::class, UsuarioSetorRepository::class);
        Vite::prefetch(concurrency: 3);
    }
}
