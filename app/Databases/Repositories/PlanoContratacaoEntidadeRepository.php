<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\PlanoContratacaoEntidadeContract;
use App\Databases\Models\PlanoContratacao;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Session;

class PlanoContratacaoEntidadeRepository implements PlanoContratacaoEntidadeContract
{
    /**
     * Constructor
     * @param PlanoContratacao $planoContratacao
     */
    public function __construct(private PlanoContratacao $planoContratacao)
    {
    }

    /**
     * Buscar registro PlanoContratacao por Id
     * @param int $id
     * @return Model
     */
    public function getById(int $id): Model
    {
        return PlanoContratacao::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    /**
     * Busca todos registros de PlanoContratacao
     * @return Collection
     */
    public function getAll(): Collection
    {
        return PlanoContratacao::query()->get();
    }

    /**
     * Pagina com filtros as PlanoContratacao
     * @param array $pagination
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $cod_setor = Session::get('setor');
        $setor_info = Session::get('setor_info');
        $is_hierarquia_1 = isset($setor_info) && $setor_info->hierarquia === "2";
        // Buscar todos os códigos de setores descendentes (recursive function)
        $setoresCodigos = $this->getDescendantSectors($cod_setor);
        // Não incluímos o próprio setor na lista

        // Construir a query usando a lista de códigos
        $query = PlanoContratacao::query();

        // Se o setor for de hierarquia 1, então também incluir planos com hierarquia_aprovacao = 0
        if ($is_hierarquia_1) {
            $query->where(function($q) use ($setoresCodigos) {
                $q->whereIn('plano_contratacao.codigo_setor', $setoresCodigos)
                    ->orWhere('plano_contratacao.hierarquia_aprovacao', 0);
            });
        } else {
            // Caso contrário, apenas os planos dos setores descendentes
            $query->whereIn('plano_contratacao.codigo_setor', $setoresCodigos);
        }

        $query->leftJoin('vw_setor_gestor', 'vw_setor_gestor.codigo_setor', '=', 'plano_contratacao.codigo_setor')
            ->leftJoin('publico.vw_sigp_funcionario', 'publico.vw_sigp_funcionario.numero_matricula', '=', 'plano_contratacao.numero_matricula_gestor_criador')
            ->select([
                'plano_contratacao.id',
                'plano_contratacao.codigo_setor',
                'plano_contratacao.exercicio',
                'plano_contratacao.valor_total',
                'plano_contratacao.status',
                'plano_contratacao.email',
                'plano_contratacao.telefone',
                'plano_contratacao.numero_matricula_gestor_criador',
                'plano_contratacao.hierarquia_aprovacao',
                'publico.vw_sigp_funcionario.nome_funcionario',
                'vw_setor_gestor.nome_setor_formatado',
            ]);

        // Filter by exercicio if provided
        if (isset($pagination['exercicio'])) {
            $query->where('plano_contratacao.exercicio', $pagination['exercicio']);
        }

        if (isset($pagination['codigo_setor'])) {
            $keyword = mb_strtolower($pagination['codigo_setor']);
            $query->whereRaw('lower(codigo_setor) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['numero_matricula_gestor'])) {
            $keyword = mb_strtolower($pagination['numero_matricula_gestor']);
            $query->whereRaw('lower(numero_matricula_gestor) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['matricula'])) {
            $keyword = mb_strtolower($pagination['matricula']);
            $query->whereRaw('lower(matricula) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['email'])) {
            $keyword = mb_strtolower($pagination['email']);
            $query->whereRaw('lower(email) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['telefone'])) {
            $keyword = mb_strtolower($pagination['telefone']);
            $query->whereRaw('lower(telefone) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['status'])) {
            $keyword = mb_strtolower($pagination['status']);
            $query->whereRaw('lower(status) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['valor_total'])) {
            $keyword = mb_strtolower($pagination['valor_total']);
            $query->whereRaw('lower(valor_total) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'codigo_setor', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }
    /**
     * Função recursiva para obter todos os setores descendentes
     * @param int $sectorId
     * @return array
     */
    private function getDescendantSectors($sectorId): array
    {
        $descendants = [];

        // Buscar filhos diretos
        $children = DB::table('setor_pca')
            ->where('codigo_setor_pai', $sectorId)
            ->pluck('codigo_setor')
            ->toArray();

        $descendants = array_merge($descendants, $children);

        // Para cada filho, buscar seus descendentes recursivamente
        foreach ($children as $childId) {
            $childDescendants = $this->getDescendantSectors($childId);
            $descendants = array_merge($descendants, $childDescendants);
        }

        return $descendants;
    }

    /**
     * Cria um novo registro de PlanoContratacao
     * @param array $params
     * @param bool $autoCommit
     * @return bool
     * @throws Exception
     */
    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $planoContratacao = new PlanoContratacao([
                'codigo_setor' => $params['codigo_setor'],
                'numero_matricula_gestor' => $params['numero_matricula_gestor'],
                'exercicio' => $params['exercicio'],
                'email' => $params['email'],
                'telefone' => $params['telefone'],
                'status' => 'E'
            ]);
            $planoContratacao->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    /**
     * Atualiza um registro existente de PlanoContratacao
     * @param int $id
     * @param array $params
     * @param bool $autoCommit
     * @return bool
     * @throws Exception
     */
    public function update(int $id, array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $planoContratacao = $this->getById($id);
            $planoContratacao->update($params);

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    /**
     * Deleta PlanoContratacao
     * @param int $id
     * @param bool $autoCommit
     * @return bool
     * @throws Exception
     */
    public function destroy(int $id, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $planoContratacao = $this->getById($id);
            $planoContratacao->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
