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

            // Calcular a hierarquia caso não esteja definida
            if (!isset($setor->hierarquia) || is_null($setor->hierarquia)) {
                $hierarquia = $this->calcularHierarquia($setor);
                // Atualizar o setor com a hierarquia calculada
                $setor->hierarquia = $hierarquia;
                $setor->save();
            }

            $resultado[] = [
                'id' => $setor->codigo_setor,
                'nome' => $infoSetor ? $infoSetor->nome_setor_formatado : 'Setor '.$setor->codigo_setor,
                'responsavel' => $infoSetor ? $infoSetor->nome_funcionario : null,
                'logon' => $infoSetor ? $infoSetor->logon : null,
                'tem_filho' => $setor->tem_setor_filho,
                'hierarquia' => $setor->hierarquia, // Adicionamos a hierarquia na resposta
                'filhos' => $filhos
            ];
        }

        return $resultado;
    }

    /**
     * Calcula a hierarquia de um setor baseado na sua posição na árvore
     *
     * @param SetorPca $setor
     * @return int
     */
    private function calcularHierarquia($setor)
    {
        // Setor raiz tem hierarquia 1
        if (is_null($setor->codigo_setor_pai)) {
            return 1;
        }

        // Busca o pai para determinar a hierarquia
        $setorPai = SetorPca::find($setor->codigo_setor_pai);

        if (!$setorPai) {
            // Se não encontrou o pai, considera como raiz
            return 1;
        }

        // Se o pai não tem hierarquia definida, calcula recursivamente
        if (!isset($setorPai->hierarquia) || is_null($setorPai->hierarquia)) {
            $hierarquiaPai = $this->calcularHierarquia($setorPai);
            $setorPai->hierarquia = $hierarquiaPai;
            $setorPai->save();
        } else {
            $hierarquiaPai = $setorPai->hierarquia;
        }

        // A hierarquia do filho é a hierarquia do pai + 1
        return $hierarquiaPai + 1;
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

        // Calcular a hierarquia apropriada
        $hierarquia = 1; // Valor padrão para raiz

        if ($request->codigo_setor_pai) {
            $setorPai = SetorPca::find($request->codigo_setor_pai);
            if ($setorPai) {
                // Se o pai não tem hierarquia definida, calcula
                if (!isset($setorPai->hierarquia) || is_null($setorPai->hierarquia)) {
                    $setorPai->hierarquia = $this->calcularHierarquia($setorPai);
                    $setorPai->save();
                }
                $hierarquia = $setorPai->hierarquia + 1;
            }
        }

        if ($existente) {
            // Atualizar o pai do setor existente e sua hierarquia
            $existente->codigo_setor_pai = $request->codigo_setor_pai;
            $existente->hierarquia = $hierarquia;
            $existente->save();

            // Atualizar hierarquias dos filhos recursivamente
            $this->atualizarHierarquiasFilhos($existente);
        } else {
            // Criar novo registro com hierarquia
            SetorPca::create([
                'codigo_setor' => $request->codigo_setor,
                'codigo_setor_pai' => $request->codigo_setor_pai,
                'ativo' => 'S',
                'tem_setor_filho' => 'N',
                'hierarquia' => $hierarquia
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

    /**
     * Atualiza recursivamente as hierarquias dos filhos de um setor
     *
     * @param SetorPca $setorPai
     */
    private function atualizarHierarquiasFilhos($setorPai)
    {
        // Buscar todos os filhos diretos
        $filhos = SetorPca::where('codigo_setor_pai', $setorPai->codigo_setor)
            ->where('ativo', 'S')
            ->get();

        foreach ($filhos as $filho) {
            // Atualizar hierarquia do filho
            $filho->hierarquia = $setorPai->hierarquia + 1;
            $filho->save();

            // Recursivamente atualizar hierarquias dos filhos deste filho
            if ($filho->tem_setor_filho == 'S') {
                $this->atualizarHierarquiasFilhos($filho);
            }
        }
    }

    public function removerSetor(Request $request)
    {
        $setor = SetorPca::find($request->codigo_setor);

        if ($setor) {
            // Se tem filhos, apenas desativa e desativa todos os filhos recursivamente
            if ($setor->tem_setor_filho == 'S') {
                $setor->ativo = 'N';
                $setor->save();

                // Desativar filhos recursivamente
                $this->desativarFilhos($setor->codigo_setor);
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

    /**
     * Desativa recursivamente todos os filhos de um setor
     *
     * @param int $codigoSetorPai
     */
    private function desativarFilhos($codigoSetorPai)
    {
        $filhos = SetorPca::where('codigo_setor_pai', $codigoSetorPai)
            ->where('ativo', 'S')
            ->get();

        foreach ($filhos as $filho) {
            $filho->ativo = 'N';
            $filho->save();

            if ($filho->tem_setor_filho == 'S') {
                $this->desativarFilhos($filho->codigo_setor);
            }
        }
    }
}
