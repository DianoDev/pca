<?php
namespace App\Http\Controllers\ItemContratacao;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\ItemContratacaoContract;
use App\Http\Requests\ItemContratacaoRequest;

class ItemContratacaoController extends Controller
{
    /**
     * Constructor
     * @param ItemContratacaoContract $itemContratacaoRepository
     */
    public function __construct(private readonly ItemContratacaoContract $itemContratacaoRepository)
    {
    }

    /**
     * Página inicial do ItemContratacao
     * @return View
     */
    public function index(): View
    {
        return view('item_contratacao.index');
    }

    /**
     * Lista registros paginados e filtrados de ItemContratacao
     * @param Request $request
     * @return JsonResponse
     */
     public function list(Request $request): JsonResponse
    {
        $dados = $this->itemContratacaoRepository->paginate($request->all())->toArray();
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
    public function create(ItemContratacaoRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->itemContratacaoRepository->create($params);
        return response()->json('success', 201);
    }

    /**
     * Obtém os detalhes de um registro específico de ItemContratacao
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $registro = $this->itemContratacaoRepository->getById($id);
        return response()->json($registro);
    }

    /**
     * Atualiza um registro existente de ItemContratacao
     * @param ItemContratacaoRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ItemContratacaoRequest $request, int $id): JsonResponse
    {
        $params = $request->except('_token');
        $this->itemContratacaoRepository->update($id, $params);
        return response()->json(['success', $params]);
    }

    /**
     * Exclui um registro específico de ItemContratacao
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $this->itemContratacaoRepository->destroy($id);
        return response()->json('success');
    }
}
