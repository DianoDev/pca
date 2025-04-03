<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\CicloContratacaoContract;
use App\Databases\Models\CicloContratacao;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class CicloContratacaoRepository implements CicloContratacaoContract
{
    /**
     * Constructor
     * @param CicloContratacao $cicloContratacao
     */
    public function __construct(private CicloContratacao $cicloContratacao)
    {
    }

    /**
     * Buscar registro CicloContratacao por Id
     * @param int $id
     * @return Model
     */
    public function getById(int $id): Model
    {
        return CicloContratacao::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    /**
     * Busca todos registros de CicloContratacao
     * @return Collection
     */
    public function getAll(): Collection
    {
        return CicloContratacao::query()->get();
    }

    /**
     * Pagina com filtros as CicloContratacao
     * @param array $pagination
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = CicloContratacao::query();

        if (isset($pagination['ano'])) {
            $keyword = mb_strtolower($pagination['ano']);
            $query->whereRaw('lower(ano) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['descricao'])) {
            $keyword = mb_strtolower($pagination['descricao']);
            $query->whereRaw('lower(descricao) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['data_inicio'])) {
            $keyword = mb_strtolower($pagination['data_inicio']);
            $query->whereRaw('lower(data_inicio) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['data_fim'])) {
            $keyword = mb_strtolower($pagination['data_fim']);
            $query->whereRaw('lower(data_fim) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'ano', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    /**
     * Cria um novo registro de CicloContratacao
     * @param array $params
     * @param bool $autoCommit
     * @return bool
     * @throws Exception
     */
    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $cicloContratacao = new CicloContratacao([
                'ano' => $params['ano'],
                'descricao' => $params['descricao'],
                'data_inicio' => $params['data_inicio'],
                'data_fim' => $params['data_fim']
            ]);
            $cicloContratacao->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    /**
     * Atualiza um registro existente de CicloContratacao
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
            $cicloContratacao = $this->getById($id);
            $cicloContratacao->update($params);

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    /**
     * Deleta CicloContratacao
     * @param int $id
     * @param bool $autoCommit
     * @return bool
     * @throws Exception
     */
    public function destroy(int $id, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $cicloContratacao = $this->getById($id);
            $cicloContratacao->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
