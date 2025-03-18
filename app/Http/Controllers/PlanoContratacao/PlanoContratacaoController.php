<?php
namespace App\Http\Controllers\PlanoContratacao;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\PlanoContratacaoContract;
use App\Http\Requests\PlanoContratacaoRequest;
use Inertia\Inertia;

class PlanoContratacaoController extends Controller
{
    /**
     * Constructor
     * @param PlanoContratacaoContract $planoContratacaoRepository
     */
    public function __construct(private readonly PlanoContratacaoContract $planoContratacaoRepository)
    {
    }

    /**
     * Página inicial do PlanoContratacao
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        return Inertia::render('PlanoContratacao/PlanoContratacaoGrid', []);
    }

    /**
     * Lista registros paginados e filtrados de PlanoContratacao
     * @param Request $request
     * @return JsonResponse
     */
     public function list(Request $request): JsonResponse
    {
        $dados = $this->planoContratacaoRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'codigo_setor' => [
                'type' => 'text',
            ],
            'numero_matricula_gestor' => [
                'type' => 'text',
            ],
            'matricula' => [
                'type' => 'text',
            ],
            'email' => [
                'type' => 'text',
            ],
            'telefone' => [
                'type' => 'text',
            ],
            'status' => [
                'type' => 'text',
            ],
            'valor_total' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }
    public function create(PlanoContratacaoRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->planoContratacaoRepository->create($params);
        return response()->json('success', 201);
    }

    /**
     * Obtém os detalhes de um registro específico de PlanoContratacao
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $registro = $this->planoContratacaoRepository->getById($id);
        return response()->json($registro);
    }

    /**
     * Atualiza um registro existente de PlanoContratacao
     * @param PlanoContratacaoRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(PlanoContratacaoRequest $request, int $id): JsonResponse
    {
        $params = $request->except('_token');
        $this->planoContratacaoRepository->update($id, $params);
        return response()->json(['success', $params]);
    }

    /**
     * Exclui um registro específico de PlanoContratacao
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $this->planoContratacaoRepository->destroy($id);
        return response()->json('success');
    }
}
