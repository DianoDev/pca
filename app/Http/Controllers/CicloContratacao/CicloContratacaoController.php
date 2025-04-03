<?php
namespace App\Http\Controllers\CicloContratacao;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\CicloContratacaoContract;
use App\Http\Requests\CicloContratacaoRequest;
use Inertia\Inertia;

class CicloContratacaoController extends Controller
{
    /**
     * Constructor
     * @param CicloContratacaoContract $cicloContratacaoRepository
     */
    public function __construct(private readonly CicloContratacaoContract $cicloContratacaoRepository)
    {
    }

    /**
     * Página inicial do CicloContratacao
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        return Inertia::render('CicloContratacao/CicloContratacaoGrid');
    }

    /**
     * Lista registros paginados e filtrados de CicloContratacao
     * @param Request $request
     * @return JsonResponse
     */
     public function list(Request $request): JsonResponse
    {
        $dados = $this->cicloContratacaoRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'ano' => [
                'type' => 'text',
            ],
            'nome_funcionario' => [
                'type' => 'text',
            ]

        ];
        return response()->json($dados);
    }
    public function create(CicloContratacaoRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->cicloContratacaoRepository->create($params);
        return response()->json('success', 201);
    }

    /**
     * Obtém os detalhes de um registro específico de CicloContratacao
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $registro = $this->cicloContratacaoRepository->getById($id);
        return response()->json($registro);
    }

    /**
     * Atualiza um registro existente de CicloContratacao
     * @param CicloContratacaoRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(CicloContratacaoRequest $request, int $id): JsonResponse
    {
        $params = $request->except('_token');
        $this->cicloContratacaoRepository->update($id, $params);
        return response()->json(['success', $params]);
    }

    /**
     * Exclui um registro específico de CicloContratacao
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $this->cicloContratacaoRepository->destroy($id);
        return response()->json('success');
    }
}
