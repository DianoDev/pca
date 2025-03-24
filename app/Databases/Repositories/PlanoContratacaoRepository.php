<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\PlanoContratacaoContract;
use App\Databases\Models\PlanoContratacao;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class PlanoContratacaoRepository implements PlanoContratacaoContract
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
        $query = PlanoContratacao::query();

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
