<?php

namespace App\Http\Controllers\PlanoContratacao;

use App\Databases\Contracts\PlanoContratacaoSetorContract;
use App\Databases\Contracts\UsuarioSetorContract;
use App\Databases\Models\AprovacaoContratacao;
use App\Databases\Models\CicloHierarquia;
use App\Databases\Models\ItemContratacao;
use App\Databases\Models\ItemProrrogacao;
use App\Databases\Models\VwSetorGestor;
use App\Http\Requests\UsuarioSetorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Databases\Models\PlanoContratacao;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class PlanoContratacaoSetorController extends Controller
{
    /**
     * Constructor
     * @param PlanoContratacaoSetorContract $planoContratacaoRepository
     */
    public function __construct(private readonly PlanoContratacaoSetorContract $planoContratacaoRepository)
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $hierarquia = Session::get('setor_info')->hierarquia;
        return Inertia::render('PlanoContratacao/PlanoContratacaoSetor', [
            'hierarquia' => $hierarquia
        ]);
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function aprovacao_contratacao($id)
    {
        $aprovacao = AprovacaoContratacao::query()->with(['plano.setor','nome_setor','usuario'])->where('id_plano_contratacao', '=', $id)->orderBy('created_at','asc')->get();
        return response()->json($aprovacao);
    }

    /**
     * Get list of resources for datatable.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        $cod_setor = Session::get('setor');
        $query = PlanoContratacao::query()
        ->where('codigo_setor','=', $cod_setor);

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

            // Validar se o status é válido (A = Aprovado ou R = Reprovado)
            if (!in_array($status, ['A','E', 'R'])) {
                return response()->json(['error' => 'Status inválido'], 400);
            }
            $cod_setor = Session::get('setor');
            // Buscar o plano
            $plano = PlanoContratacao::findOrFail($id);
            $Usuario = auth()->user();
            $matricula = VwsetorGestor::query()->where('logon','=',$Usuario->name)->first();
            $aprovacaoContratacao = new AprovacaoContratacao([
                'id_plano_contratacao' => $id,
                'codigo_setor' => $cod_setor,
                'numero_matricula_aprovacao' => $matricula->responsavel,
                'status' => 'E'
            ]);
            $aprovacaoContratacao->save();
            // Atualizar o status
            $plano->status = $status;
            $plano->save();

            return response()->json(['success' => true, 'message' => 'Status atualizado com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get all years that have plans.
     *
     * @return JsonResponse
     */
    public function getYears()
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
        $hierarquia = Session::get('setor_info')->hierarquia;

        if (!$exercicio) {
            return response()->json(['exists' => false]);
        }

        // Primeiro, buscar o plano de contratação
        $planoContratacao = PlanoContratacao::query()
            ->with(['gestor', 'ciclo'])
            ->where('codigo_setor', $cod_setor)
            ->where('exercicio', $exercicio)
            ->first();

        // Valores padrão
        $item_prorrogacao = 0;
        $item_contratacao = 0;

        if ($planoContratacao) {
            // Buscar dados da hierarquia
            $cicloHierarquia = CicloHierarquia::query()
                ->where('hierarquia', $hierarquia)
                ->first();

            // Adicionar informações da hierarquia ao resultado se existir
            if ($cicloHierarquia) {
                $planoContratacao->ciclo_hierarquia = $cicloHierarquia;
            }

            // Buscar somas usando o ID do plano, não o objeto inteiro
            $item_prorrogacao = ItemProrrogacao::query()
                ->where('id_plano_contratacao', $planoContratacao->id)
                ->sum('valor_global');

            $item_contratacao = ItemContratacao::query()
                ->where('id_plano_contratacao', $planoContratacao->id)
                ->sum('valor_total');
        }

        return response()->json([
            'exists' => $planoContratacao,
            'valor_prorrogacao' => $item_prorrogacao,
            'valor_contratacao' => $item_contratacao
        ]);
    }
}
