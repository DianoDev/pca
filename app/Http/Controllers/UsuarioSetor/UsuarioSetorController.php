<?php
namespace App\Http\Controllers\UsuarioSetor;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\UsuarioSetorContract;
use App\Http\Requests\UsuarioSetorRequest;
use Inertia\Inertia;

class UsuarioSetorController extends Controller
{
    /**
     * Constructor
     * @param UsuarioSetorContract $usuarioSetorRepository
     */
    public function __construct(private readonly UsuarioSetorContract $usuarioSetorRepository)
    {
    }

    /**
     * Página inicial do UsuarioSetor
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        return Inertia::render('UsuarioSetor/UsuarioSetorGrid', []);
    }

    /**
     * Lista registros paginados e filtrados de UsuarioSetor
     * @param Request $request
     * @return JsonResponse
     */
     public function list(Request $request): JsonResponse
    {
        $dados = $this->usuarioSetorRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'codigo_setor' => [
                'type' => 'text',
            ],
            'numero_matricula' => [
                'type' => 'text',
            ],
            'numero_matricula_gestor' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }
    public function create(UsuarioSetorRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->usuarioSetorRepository->create($params);
        return response()->json('success', 201);
    }

    /**
     * Obtém os detalhes de um registro específico de UsuarioSetor
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $registro = $this->usuarioSetorRepository->getById($id);
        return response()->json($registro);
    }

    /**
     * Atualiza um registro existente de UsuarioSetor
     * @param UsuarioSetorRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UsuarioSetorRequest $request, int $id): JsonResponse
    {
        $params = $request->except('_token');
        $this->usuarioSetorRepository->update($id, $params);
        return response()->json(['success', $params]);
    }

    /**
     * Exclui um registro específico de UsuarioSetor
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $this->usuarioSetorRepository->destroy($id);
        return response()->json('success');
    }
}
