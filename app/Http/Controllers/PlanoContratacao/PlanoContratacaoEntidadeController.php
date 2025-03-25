<?php

namespace App\Http\Controllers\PlanoContratacao;

use App\Databases\Contracts\PlanoContratacaoEntidadeContract;
use App\Databases\Contracts\UsuarioTceContract;
use App\Databases\Models\VwSetorGestor;
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
    public function validacao()
    {
        return Inertia::render('PlanoContratacao/PlanoContratacaoValidacao');
    }

    /**
     * Get a specific plan by ID for validation.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $plano = PlanoContratacao::query()
                ->with(['gestor', 'itensContratacao', 'itensProrrogacao'])
                ->findOrFail($id);

            // Mapear os dados para incluir informações do setor e gestor
            $resultado = [
                'id' => $plano->id,
                'codigo_setor' => $plano->codigo_setor,
                'exercicio' => $plano->exercicio,
                'valor_total' => $plano->valor_total,
                'status' => $plano->status,
                'email' => $plano->email,
                'telefone' => $plano->telefone,
                'numero_matricula_gestor' => $plano->numero_matricula_gestor,
                'setor_nome' => $plano->gestor->nome_setor_formatado ?? 'Setor não informado',
                'gestor_nome' => $plano->gestor->nome_funcionario ?? 'Gestor não informado',
            ];

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Plano não encontrado'], 404);
        }
    }

    /**
     * Update the plan status (approve or reject).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $status = $request->input('status');

            // Validar se o status é válido (A = Aprovado ou R = Reprovado)
            if (!in_array($status, ['A', 'R'])) {
                return response()->json(['error' => 'Status inválido'], 400);
            }

            // Buscar o plano
            $plano = PlanoContratacao::findOrFail($id);

            // Atualizar o status
            $plano->status = $status;
            $plano->save();

            return response()->json(['success' => true, 'message' => 'Status atualizado com sucesso']);
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
     * @param  int  $id
     * @return JsonResponse
     */
    public function gestorInfo()
    {
        $Usuario = auth()->user();
        $gestor = VwSetorGestor::query()->where('logon','=', $Usuario->name)->first();
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
            ->where('codigo_setor',$cod_setor)
            ->select('exercicio')
            ->distinct()
            ->orderBy('exercicio', 'desc')
            ->pluck('exercicio')
            ->map(function($year) {
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
            ->where('codigo_Tce',$cod_setor)
            ->where('exercicio', $exercicio)->first();

        return response()->json(['exists' => $exists]);
    }
}
