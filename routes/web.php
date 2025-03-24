<?php

use App\Http\Controllers\ItemProrrogacao\ItemProrrogacaoController;
use App\Http\Controllers\ItemContratacao\ItemContratacaoController;
use App\Http\Controllers\PlanoContratacao\PlanoContratacaoSetorController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Organograma\OrganogramaController;
use App\Http\Controllers\PlanoContratacao\PlanoContratacaoTceController;
use App\Http\Controllers\UsuarioSetor\UsuarioSetorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard/Dashboard');
})->middleware(['auth', 'setor'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['prefix' => 'usuario-setor'], function () {
    Route::get('/', [UsuarioSetorController::class, 'index'])->name('usuario_setor.index');
    Route::get('/list', [UsuarioSetorController::class, 'list'])->name('usuario_setor.list');
    Route::get('/{id}', [UsuarioSetorController::class, 'edit'])->name('usuario_setor.edit');
    Route::post('/', [UsuarioSetorController::class, 'create'])->name('usuario_setor.create');
    Route::post('/{id}', [UsuarioSetorController::class, 'update'])->name('usuario_setor.update');
    Route::delete('/{id}', [UsuarioSetorController::class, 'delete'])->name('usuario_setor.delete');
});

Route::group(['prefix' => 'organograma','middleware' => ['auth']], function () {
    Route::get('/', [OrganogramaController::class, 'index'])->name('organograma.index');
    Route::get('/buscar-setores', [OrganogramaController::class, 'buscarSetores'])->name('organograma.buscar-setores');
    Route::post('/adicionar-filho', [OrganogramaController::class, 'adicionarFilho'])->name('organograma.adicionar-filho');
    Route::delete('/remover-setor/{id}', [OrganogramaController::class, 'removerSetor'])->name('organograma.remover-setor');
});

Route::group(['prefix' => 'setor','middleware' => ['auth']], function () {
    Route::get('/', [AuthController::class, 'setor'])->name('setor.setor');
    Route::post('/selecionar-setor', [AuthController::class, 'seleciona'])->name('setor.seleciona');
});
require __DIR__.'/auth.php';

Route::group(['prefix' => 'plano-contratacao-setor','middleware' => ['auth','setor']], function () {
    Route::get('/', [PlanoContratacaoSetorController::class, 'index'])->name('plano_contratacao.index');
    Route::get('/list', [PlanoContratacaoSetorController::class, 'list'])->name('plano_contratacao.list');
    Route::get('/years', [PlanoContratacaoSetorController::class, 'getYears'])->name('plano_contratacao.getYears');
    Route::get('/exists', [PlanoContratacaoSetorController::class, 'exists'])->name('plano_contratacao.exists');
    Route::get('/gestorInfo', [PlanoContratacaoSetorController::class, 'gestorInfo'])->name('plano_contratacao.edit');
    Route::post('/', [PlanoContratacaoSetorController::class, 'create'])->name('plano_contratacao.create');
    Route::post('/{id}', [PlanoContratacaoSetorController::class, 'update'])->name('plano_contratacao.update');
    Route::delete('/{id}', [PlanoContratacaoSetorController::class, 'delete'])->name('plano_contratacao.delete');
});
Route::group(['prefix' => 'item-contratacao-setor','middleware' => ['auth','setor']], function () {
    Route::get('/', [ItemContratacaoController::class, 'index'])->name('item_contratacao.index');
    Route::get('/{id}/list', [ItemContratacaoController::class, 'list'])->name('item_contratacao.list');
    Route::get('/{id}', [ItemContratacaoController::class, 'edit'])->name('item_contratacao.edit');
    Route::post('/', [ItemContratacaoController::class, 'create'])->name('item_contratacao.create');
    Route::post('/{id}', [ItemContratacaoController::class, 'update'])->name('item_contratacao.update');
    Route::delete('/{id}', [ItemContratacaoController::class, 'delete'])->name('item_contratacao.delete');
});

Route::group(['prefix' => 'item-prorrogacao-setor','middleware' => ['auth','setor']], function () {
    Route::get('/', [ItemProrrogacaoController::class, 'index'])->name('item_prorrogacao.index');
    Route::get('/{id}/list', [ItemProrrogacaoController::class, 'list'])->name('item_prorrogacao.list');
    Route::get('/{id}', [ItemProrrogacaoController::class, 'edit'])->name('item_prorrogacao.edit');
    Route::post('/', [ItemProrrogacaoController::class, 'create'])->name('item_prorrogacao.create');
    Route::post('/{id}', [ItemProrrogacaoController::class, 'update'])->name('item_prorrogacao.update');
    Route::delete('/{id}', [ItemProrrogacaoController::class, 'delete'])->name('item_prorrogacao.delete');
});

Route::group(['prefix' => 'plano-contratacao-tce','middleware' => ['auth','setor']], function () {
    Route::get('/', [PlanoContratacaoTceController::class, 'index'])->name('plano_contratacao.index');
    Route::get('/list', [PlanoContratacaoTceController::class, 'list'])->name('plano_contratacao.list');
    Route::get('/years', [PlanoContratacaoTceController::class, 'getYears'])->name('plano_contratacao.getYears');
    Route::get('/exists', [PlanoContratacaoTceController::class, 'exists'])->name('plano_contratacao.exists');
    Route::get('/gestorInfo', [PlanoContratacaoTceController::class, 'gestorInfo'])->name('plano_contratacao.edit');
    Route::post('/', [PlanoContratacaoTceController::class, 'create'])->name('plano_contratacao.create');
    Route::post('/{id}', [PlanoContratacaoTceController::class, 'update'])->name('plano_contratacao.update');
    Route::delete('/{id}', [PlanoContratacaoTceController::class, 'delete'])->name('plano_contratacao.delete');
});
