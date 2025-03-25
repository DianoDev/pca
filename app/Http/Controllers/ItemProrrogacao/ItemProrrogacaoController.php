<?php
namespace App\Http\Controllers\ItemProrrogacao;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\ItemProrrogacaoContract;
use App\Http\Requests\ItemProrrogacaoRequest;

class ItemProrrogacaoController extends Controller
{
    /**
     * Constructor
     * @param ItemProrrogacaoContract $itemProrrogacaoRepository
     */
    public function __construct(private readonly ItemProrrogacaoContract $itemProrrogacaoRepository)
    {
    }

    /**
     * Página inicial do ItemProrrogacao
     * @return View
     */
    public function index(): View
    {
        return view('item_prorrogacao.index');
    }

    /**
     * Lista registros paginados e filtrados de ItemProrrogacao
     * @param Request $request
     * @return JsonResponse
     */
     public function list($id,Request $request): JsonResponse
    {
        $requestData = array_merge($request->all(), ['id_plano_contratacao' => $id]);
        $dados = $this->itemProrrogacaoRepository->paginate($requestData)->toArray();
        $dados['filter_options'] = [
            'objeto' => [
                'type' => 'text',
            ],
            'numero' => [
                'type' => 'text',
            ],
            'empresa' => [
                'type' => 'text',
            ],
            'cnpj' => [
                'type' => 'text',
            ],
            'valor_global' => [
                'type' => 'text',
            ],
            'termino_vigencia' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }
    public function create(ItemProrrogacaoRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->itemProrrogacaoRepository->create($params);
        return response()->json('success', 201);
    }

    /**
     * Obtém os detalhes de um registro específico de ItemProrrogacao
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $registro = $this->itemProrrogacaoRepository->getById($id);
        return response()->json($registro);
    }

    /**
     * Atualiza um registro existente de ItemProrrogacao
     * @param ItemProrrogacaoRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ItemProrrogacaoRequest $request, int $id): JsonResponse
    {
        $params = $request->except('_token');
        $this->itemProrrogacaoRepository->update($id, $params);
        return response()->json(['success', $params]);
    }

    /**
     * Atualiza um registro existente de ItemContratacao
     * @param ItemContratacaoRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(ItemProrrogacaoRequest $request, int $id): JsonResponse
    {
        $params = $request->except('_token');
        $this->itemProrrogacaoRepository->updateStatus($id, $params);
        return response()->json(['success', $params]);
    }

    /**
     * Exclui um registro específico de ItemProrrogacao
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $this->itemProrrogacaoRepository->destroy($id);
        return response()->json('success');
    }
}
