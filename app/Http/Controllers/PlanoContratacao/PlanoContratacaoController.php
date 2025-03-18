<?php

namespace App\Http\Controllers\PlanoContratacao;

use App\Databases\Contracts\PlanoContratacaoContract;
use App\Databases\Contracts\UsuarioSetorContract;
use App\Databases\Models\VwSetorGestor;
use App\Http\Requests\UsuarioSetorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Databases\Models\PlanoContratacao;
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
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('PlanoContratacao/PlanoContratacaoGrid');
    }

    public function create(UsuarioSetorRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->planoContratacaoRepository->create($params);
        return response()->json('success', 201);
    }

    /**
     * Obtém os detalhes de um registro específico de UsuarioSetor
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $registro = $this->planoContratacaoRepository->getById($id);
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
        $this->planoContratacaoRepository->update($id, $params);
        return response()->json(['success', $params]);
    }

    /**
     * Exclui um registro específico de UsuarioSetor
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
    public function list(Request $request)
    {
        $query = PlanoContratacao::query();

        // Filter by exercicio if provided
        if ($request->has('exercicio')) {
            $query->where('exercicio', $request->exercicio);
        }

        // Handle sorting
        if ($request->has('sort') && $request->has('order')) {
            $query->orderBy($request->sort, $request->order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $total = $query->count();
        $results = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return response()->json([
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ]);
    }

    /**
     * Get all years that have plans.
     *
     * @return JsonResponse
     */
    public function getYears()
    {
        $years = PlanoContratacao::select('exercicio')
            ->distinct()
            ->orderBy('exercicio', 'desc')
            ->pluck('exercicio')
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
        $exercicio = $request->input('exercicio');

        if (!$exercicio) {
            return response()->json(['exists' => false]);
        }

        $exists = PlanoContratacao::where('exercicio', $exercicio)->exists();

        return response()->json(['exists' => $exists]);
    }
}
