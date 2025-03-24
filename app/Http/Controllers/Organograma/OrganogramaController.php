<?php

namespace App\Http\Controllers\Organograma;
use App\Http\Controllers\Controller;
use App\Databases\Models\SetorPCA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrganogramaController extends Controller
{
    public function index()
    {
        // Buscar todos os setores raiz (sem pai)
        $setores = SetorPca::whereNull('codigo_setor_pai')
            ->where('ativo', 'S')
            ->get();

        // Construir árvore de setores recursivamente
        $arvoreSetores = $this->construirArvore($setores);

        return Inertia::render('Organograma/Organograma', [
            'arvoreSetores' => $arvoreSetores,
        ]);
    }

    private function construirArvore($setores)
    {
        $resultado = [];

        foreach ($setores as $setor) {
            // Buscar informações adicionais da view
            $infoSetor = DB::table('SCMPCA.VW_SETOR_GESTOR')
                ->where('CODIGO_SETOR', $setor->codigo_setor)
                ->first();

            $filhos = [];
            if ($setor->tem_setor_filho == 'S') {
                $setoresFilhos = SetorPca::where('codigo_setor_pai', $setor->codigo_setor)
                    ->where('ativo', 'S')
                    ->get();
                $filhos = $this->construirArvore($setoresFilhos);
            }

            $resultado[] = [
                'id' => $setor->codigo_setor,
                'nome' => $infoSetor ? $infoSetor->nome_setor_formatado : 'Setor '.$setor->codigo_setor,
                'responsavel' => $infoSetor ? $infoSetor->nome_funcionario : null,
                'logon' => $infoSetor ? $infoSetor->logon : null,
                'tem_filho' => $setor->tem_setor_filho,
                'filhos' => $filhos
            ];
        }

        return $resultado;
    }

    public function buscarSetores(Request $request)
    {
        $termo = strtolower($request->input('termo', ''));

        $setores = DB::table('SCMPCA.VW_SETOR_GESTOR')
            ->whereRaw('LOWER(NOME_SETOR_FORMATADO) like ?', ["%{$termo}%"])
            ->select('CODIGO_SETOR', 'NOME_SETOR_FORMATADO', 'NOME_FUNCIONARIO', 'LOGON')
            ->limit(10)
            ->get();

        return response()->json($setores);
    }

    public function formAdicionarSetor(Request $request)
    {
        $codigoSetorPai = $request->input('codigo_setor_pai');
        $setorPai = null;

        if ($codigoSetorPai) {
            // Buscar informações do setor pai
            $setorPaiInfo = DB::table('SCMPCA.VW_SETOR_GESTOR')
                ->where('CODIGO_SETOR', $codigoSetorPai)
                ->first();

            if ($setorPaiInfo) {
                $setorPai = [
                    'id' => $codigoSetorPai,
                    'nome' => $setorPaiInfo->NOME_SETOR_FORMATADO
                ];
            }
        }

        return Inertia::render('Organograma/Componentes/FormAdicionarSetor', [
            'setorPai' => $setorPai,
            'ehSetorRaiz' => !$codigoSetorPai
        ]);
    }

    public function adicionarFilho(Request $request)
    {

        // Verificar se já existe relacionamento
        $params = $request->except('_token');
        $existente = SetorPca::where('codigo_setor', $request->codigo_setor)->first();

        if ($existente) {
            // Atualizar o pai do setor existente
            $existente->codigo_setor_pai = $request->codigo_setor_pai;
            $existente->save();
        } else {
            // Criar novo registro
            SetorPca::create([
                'codigo_setor' => $request->codigo_setor,
                'codigo_setor_pai' => $request->codigo_setor_pai,
                'ativo' => 'S',
                'tem_setor_filho' => 'N',
            ]);
        }

        // Atualizar o setor pai para ter filhos
        $setorPai = SetorPca::find($request->codigo_setor_pai);
        if ($setorPai && $setorPai->tem_setor_filho == 'N') {
            $setorPai->tem_setor_filho = 'S';
            $setorPai->save();
        }

        return redirect()->back()->with('success', 'Setor adicionado com sucesso!');
    }

    public function removerSetor(Request $request)
    {

        $setor = SetorPca::find($request->codigo_setor);

        if ($setor) {
            // Se tem filhos, apenas desativa
            if ($setor->tem_setor_filho == 'S') {
                $setor->ativo = 'N';
                $setor->save();
            } else {
                // Se não tem filhos, remove o registro
                $setor->delete();
            }

            // Verificar se o pai ainda tem outros filhos
            if ($setor->codigo_setor_pai) {
                $outrosFilhos = SetorPca::where('codigo_setor_pai', $setor->codigo_setor_pai)
                    ->where('ativo', 'S')
                    ->where('codigo_setor', '!=', $setor->codigo_setor)
                    ->count();

                if ($outrosFilhos == 0) {
                    $setorPai = SetorPca::find($setor->codigo_setor_pai);
                    if ($setorPai) {
                        $setorPai->tem_setor_filho = 'N';
                        $setorPai->save();
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Setor removido com sucesso!');
    }
}
