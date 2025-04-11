<?php

namespace App\Http\Controllers\PlanoContratacao;

use App\Databases\Contracts\PlanoContratacaoEntidadeContract;
use App\Databases\Contracts\UsuarioTceContract;
use App\Databases\Models\AprovacaoContratacao;
use App\Databases\Models\ItemContratacao;
use App\Databases\Models\ItemProrrogacao;
use App\Databases\Models\SetorPCA;
use App\Databases\Models\VwSetorGestor;
use App\Databases\Models\VwSigpSetorSecorp;
use App\Databases\Models\VwTceGestor;
use App\Http\Requests\UsuarioTceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Databases\Models\PlanoContratacao;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class PlanoContratacaoEntidadeController extends Controller
{
    /**
     * Constructor
     * @param PlanoContratacaoEntidadeContract $planoContratacaoRepository
     */
    public function __construct(private readonly PlanoContratacaoEntidadeContract $planoContratacaoRepository)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('PlanoContratacao/PlanoContratacaoEntidade');
    }

    /**
     * Display the validation page for a plan.
     *
     * @return \Inertia\Response
     */
    public function validacao(int $id)
    {
        try {
            // Buscar o plano com os relacionamentos necessários
            $plano = PlanoContratacao::query()
                ->leftJoin('vw_setor_gestor', 'vw_setor_gestor.codigo_setor', '=', 'plano_contratacao.codigo_setor')
                ->leftJoin('publico.vw_sigp_funcionario', 'publico.vw_sigp_funcionario.numero_matricula', '=', 'plano_contratacao.numero_matricula_gestor_criador')
                ->leftJoin('setor_pca', 'setor_pca.codigo_setor', '=', 'plano_contratacao.codigo_setor')
                ->select([
                    'plano_contratacao.id',
                    'plano_contratacao.codigo_setor',
                    'plano_contratacao.exercicio',
                    'plano_contratacao.valor_total',
                    'plano_contratacao.status',
                    'plano_contratacao.email',
                    'plano_contratacao.hierarquia_aprovacao',
                    'plano_contratacao.telefone',
                    'plano_contratacao.numero_matricula_gestor_criador',
                    'setor_pca.hierarquia',
                    'vw_setor_gestor.nome_setor_formatado as setor_nome',
                    'publico.vw_sigp_funcionario.nome_funcionario as gestor_nome'
                ])
                ->where('plano_contratacao.id', $id)
                ->first();
            $setor_info = session()->get('setor_info');
            if($setor_info->hierarquia === $plano->hierarquia_aprovacao){
                $setor_aprovacao = VwSigpSetorSecorp::query()->where('codigo_setor','=',$setor_info->codigo_setor)->first();
                $setor_aprovacao = $setor_aprovacao->nome_setor;
            }
            elseif($plano->hierarquia_aprovacao === "4"){
                $setor = SetorPCA::query()->where('codigo_setor', $plano->codigo_setor)->first();
                $setor_aprovacao = VwSigpSetorSecorp::query()->where('codigo_setor','=',$setor->codigo_setor)->first();
                $setor_aprovacao = $setor_aprovacao->nome_setor;
            }
            elseif($plano->hierarquia_aprovacao === "0"){
                $setor = SetorPCA::query()->where('hierarquia', '=','2')->first();
                $setor_aprovacao = VwSigpSetorSecorp::query()->where('codigo_setor','=',$setor->codigo_setor)->first();
                $setor_aprovacao = $setor_aprovacao->nome_setor;
            }
            elseif($plano->hierarquia_aprovacao === "-1"){
                $setor_aprovacao = 'Plano Aprovado';
            }
            else{
                $setor = SetorPCA::query()->where('hierarquia', $plano->hierarquia_aprovacao)->first();
                $setor_aprovacao = VwSigpSetorSecorp::query()->where('codigo_setor','=',$setor->codigo_setor)->first();
                $setor_aprovacao = $setor_aprovacao->nome_setor;
            }

            $item_prorrogacao = ItemProrrogacao::query()
                ->where('id_plano_contratacao', $plano->id)
                ->sum('valor_global');

            $item_contratacao = ItemContratacao::query()
                ->where('id_plano_contratacao', $plano->id)
                ->sum('valor_total');
            // Passar o plano diretamente para o componente
            return Inertia::render('PlanoContratacao/PlanoContratacaoValidacao', [
                'plano' => $plano,
                'hierarquia_setor' => $setor_info->hierarquia,
                'setor_aprovacao_atual' => $setor_aprovacao,
                'valor_prorrogacao' => $item_prorrogacao,
                'valor_contratacao' => $item_contratacao
            ]);
        } catch (\Exception $e) {
            // Em caso de erro, passar mensagem de erro
            return Inertia::render('PlanoContratacao/PlanoContratacaoValidacao', [
                'plano' => null,
                'erro' => 'Erro ao buscar plano: ' . $e->getMessage(),
            ]);
        }
    }


    /**
     * Update the plan status (approve or reject).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');
            $cod_setor = Session::get('setor');
            $plano = PlanoContratacao::findOrFail($id);
            $Usuario = auth()->user();
            $matricula = VwsetorGestor::query()->where('logon','=',$Usuario->name)->first();

            // Recuperar todos os itens que não estão reprovados
            $itens_contratacao = ItemContratacao::query()->where('id_plano_contratacao', $id)->where('status','!=','R')->get();
            $itens_prorrogacao = ItemProrrogacao::query()->where('id_plano_contratacao', $id)->where('status','!=','R')->get();

            // Atualizar o status de todos os itens de contratação
            foreach ($itens_contratacao as $item) {
                $item->status = $status;
                $item->save();
            }

            // Atualizar o status de todos os itens de prorrogação
            foreach ($itens_prorrogacao as $item) {
                $item->status = $status;
                $item->save();
            }

            $plano->hierarquia_aprovacao = $plano->hierarquia_aprovacao - 1;
            $plano->save();

            $aprovacaoContratacao = new AprovacaoContratacao([
                'id_plano_contratacao' => $id,
                'codigo_setor' => $cod_setor,
                'numero_matricula_aprovacao' => $matricula->responsavel,
                'status' => $status // Usar o mesmo status que foi solicitado
            ]);
            $aprovacaoContratacao->save();

            return response()->json([
                'success' => true,
                'message' => 'Status do plano e de todos os itens atualizado com sucesso',
                'itens_contratacao_atualizados' => $itens_contratacao->count(),
                'itens_prorrogacao_atualizados' => $itens_prorrogacao->count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar status: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Update the plan status (approve or reject).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function aprovaplano(int $id, Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');
            $cod_setor = Session::get('setor');
            $plano = PlanoContratacao::findOrFail($id);
            $Usuario = auth()->user();
            $matricula = VwsetorGestor::query()->where('logon','=',$Usuario->name)->first();


            $plano->hierarquia_aprovacao = $plano->hierarquia_aprovacao - 1;
            $plano->status  = $status;
            $plano->save();

            // Recuperar todos os itens que não estão reprovados
            $itens_contratacao = ItemContratacao::query()->where('id_plano_contratacao', $id)->where('status','!=','R')->get();
            $itens_prorrogacao = ItemProrrogacao::query()->where('id_plano_contratacao', $id)->where('status','!=','R')->get();

            // Atualizar o status de todos os itens de contratação
            foreach ($itens_contratacao as $item) {
                $item->status = $status;
                $item->save();
            }

            // Atualizar o status de todos os itens de prorrogação
            foreach ($itens_prorrogacao as $item) {
                $item->status = $status;
                $item->save();
            }

            $aprovacaoContratacao = new AprovacaoContratacao([
                'id_plano_contratacao' => $id,
                'codigo_setor' => $cod_setor,
                'numero_matricula_aprovacao' => $matricula->responsavel,
                'status' => $status // Usar o mesmo status que foi solicitado
            ]);
            $aprovacaoContratacao->save();
            return response()->json([
                'success' => true,
                'message' => 'Status do plano e de todos os itens atualizado com sucesso',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar status: ' . $e->getMessage()], 500);
        }
    }


    public function create(Request $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->planoContratacaoRepository->create($params);
        return response()->json('success', 201);
    }

    /**
     * Obtém os detalhes de um registro específico de UsuarioTce
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $registro = $this->planoContratacaoRepository->getById($id);
        return response()->json($registro);
    }

    /**
     * Atualiza um registro existente de UsuarioTce
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $params = $request->except('_token');
        $this->planoContratacaoRepository->update($id, $params);
        return response()->json(['success', $params]);
    }

    /**
     * Exclui um registro específico de UsuarioTce
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $this->planoContratacaoRepository->destroy($id);
        return response()->json('success');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function gestorInfo()
    {
        $Usuario = auth()->user();
        $gestor = VwSetorGestor::query()->where('logon', '=', $Usuario->name)->first();
        return response()->json($gestor);
    }

    /**
     * Get list of resources for datatable.
     *
     * @param Request $request
     * @return JsonResponse
     */
    /**
     * Lista registros paginados e filtrados de ItemContratacao
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        // Busca os dados paginados com os filtros atualizados
        $dados = $this->planoContratacaoRepository->paginate($request->all())->toArray();

        $dados['filter_options'] = [
            'id_plano_contratacao' => [
                'type' => 'text',
            ],
            'descricao' => [
                'type' => 'text',
            ],
            'unidade_medida' => [
                'type' => 'text',
            ],
            'quantidade' => [
                'type' => 'text',
            ],
            'valor_unitario_estimado' => [
                'type' => 'text',
            ],
            'valor_total' => [
                'type' => 'text',
            ],
            'data_desejada' => [
                'type' => 'text',
            ],
            'classificacao' => [
                'type' => 'text',
            ],
            'status' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }

    /**
     * Get all years that have plans.
     *
     * @return JsonResponse
     */
    public function getYears(): JsonResponse
    {
        $cod_setor = Session::get('setor');
        $years = PlanoContratacao::query()
            ->where('codigo_setor', $cod_setor)
            ->select('exercicio')
            ->distinct()
            ->orderBy('exercicio', 'desc')
            ->pluck('exercicio')
            ->map(function ($year) {
                return (int)$year; // Ensure years are integers
            })
            ->toArray();

        return response()->json(['years' => $years]);
    }

    /**
     * Check if a plan exists for a given year.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function exists(Request $request)
    {
        $cod_setor = Session::get('setor');
        $exercicio = $request->input('exercicio');

        if (!$exercicio) {
            return response()->json(['exists' => false]);
        }

        $exists = PlanoContratacao::query()->with('gestor')
            ->where('codigo_Tce', $cod_setor)
            ->where('exercicio', $exercicio)->first();

        return response()->json(['exists' => $exists]);
    }
}
