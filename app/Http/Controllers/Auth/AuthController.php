<?php

namespace App\Http\Controllers\Auth;

use App\Databases\Models\SetorPCA;
use App\Databases\Models\UsuarioSetor;
use App\Databases\Models\VwSetorGestor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function setor(Request $request): Response
    {
        $Usuario = auth()->user();
        $adm_setor = VwSetorGestor::query()
            ->where('logon', '=', $Usuario->name)
            ->whereIn('codigo_setor', function($query) {
                $query->select('codigo_setor')
                    ->from('setor_pca');
            })
            ->get();
        $func_setor = UsuarioSetor::query()->where('numero_matricula','=', $Usuario->nome)->get();
        return Inertia::render('Auth/Setor', [
            'adm_setor' => $adm_setor,
            'func_setor' => $func_setor,
        ]);
    }

    public function seleciona(Request $request): \Illuminate\Http\RedirectResponse
    {
        $codigoSetor = $request->input('codigo_setor');
        $request->session()->put('setor', $codigoSetor);
        $setor_info = SetorPCA::query()->where('codigo_setor','=', $codigoSetor)->first();
        $request->session()->put('setor_info', $setor_info);
        return redirect()->route('dashboard');
    }
}
