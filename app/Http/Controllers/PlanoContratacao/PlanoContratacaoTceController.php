<?php

namespace App\Http\Controllers\PlanoContratacao;

use App\Databases\Contracts\PlanoContratacaoTceContract;
use App\Databases\Contracts\UsuarioTceContract;
use App\Databases\Models\VwTceGestor;
use App\Http\Requests\UsuarioTceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Databases\Models\PlanoContratacao;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class PlanoContratacaoTceController extends Controller
{
    /**
     * Constructor
     * @param PlanoContratacaoTceContract $planoContratacaoRepository
     */
    public function __construct(private readonly PlanoContratacaoTceContract $planoContratacaoRepository)
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('PlanoContratacaoTce/PlanoContratacaoTce');
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
        $gestor = VwTceGestor::query()->where('logon','=', $Usuario->name)->first();
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
        $cod_Tce = Session::get('Tce');
        $query = PlanoContratacao::query()
            ->where('codigo_Tce','=', $cod_Tce);

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
    public function getYears(): JsonResponse
    {
        $cod_Tce = Session::get('Tce');
        $years = PlanoContratacao::query()
            ->where('codigo_Tce',$cod_Tce)
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
        $cod_Tce = Session::get('Tce');
        $exercicio = $request->input('exercicio');

        if (!$exercicio) {
            return response()->json(['exists' => false]);
        }

        $exists = PlanoContratacao::query()->with('gestor')
            ->where('codigo_Tce',$cod_Tce)
            ->where('exercicio', $exercicio)->first();

        return response()->json(['exists' => $exists]);
    }
}
