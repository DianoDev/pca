<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\ItemContratacaoContract;
use App\Databases\Models\ItemContratacao;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class ItemContratacaoRepository implements ItemContratacaoContract
{
    /**
     * Constructor
     * @param ItemContratacao $itemContratacao
     */
    public function __construct(private ItemContratacao $itemContratacao)
    {
    }

    /**
     * Buscar registro ItemContratacao por Id
     * @param int $id
     * @return Model
     */
    public function getById(int $id): Model
    {
        return ItemContratacao::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    /**
     * Busca todos registros de ItemContratacao
     * @return Collection
     */
    public function getAll(): Collection
    {
        return ItemContratacao::query()->get();
    }

    /**
     * Pagina com filtros as ItemContratacao
     * @param array $pagination
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = ItemContratacao::query()->where('id_plano_contratacao','=', $pagination['id_plano_contratacao']);

        if (isset($pagination['id_plano_contratacao'])) {
            $keyword = mb_strtolower($pagination['id_plano_contratacao']);
            $query->whereRaw('lower(id_plano_contratacao) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['descricao'])) {
            $keyword = mb_strtolower($pagination['descricao']);
            $query->whereRaw('lower(descricao) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['unidade_medida'])) {
            $keyword = mb_strtolower($pagination['unidade_medida']);
            $query->whereRaw('lower(unidade_medida) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['quantidade'])) {
            $keyword = mb_strtolower($pagination['quantidade']);
            $query->whereRaw('lower(quantidade) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['valor_unitario_estimado'])) {
            $keyword = mb_strtolower($pagination['valor_unitario_estimado']);
            $query->whereRaw('lower(valor_unitario_estimado) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['valor_total'])) {
            $keyword = mb_strtolower($pagination['valor_total']);
            $query->whereRaw('lower(valor_total) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['data_desejada'])) {
            $keyword = mb_strtolower($pagination['data_desejada']);
            $query->whereRaw('lower(data_desejada) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['classificacao'])) {
            $keyword = mb_strtolower($pagination['classificacao']);
            $query->whereRaw('lower(classificacao) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['status'])) {
            $keyword = mb_strtolower($pagination['status']);
            $query->whereRaw('lower(status) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'valor_total', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    /**
     * Cria um novo registro de ItemContratacao
     * @param array $params
     * @param bool $autoCommit
     * @return bool
     * @throws Exception
     */
    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $itemContratacao = new ItemContratacao([
                'id_plano_contratacao' => $params['id_plano_contratacao'],
                'descricao' => $params['descricao'],
                'unidade_medida' => $params['unidade_medida'],
                'quantidade' => $params['quantidade'],
                'valor_unitario_estimado' => $params['valor_unitario_estimado'],
                'valor_total' => $params['valor_total'],
                'data_desejada' => $params['data_desejada'],
                'classificacao' => $params['classificacao'],
                'status' => 'E'
            ]);
            $itemContratacao->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    /**
     * Atualiza um registro existente de ItemContratacao
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
            $itemContratacao = $this->getById($id);
            $itemContratacao->update($params);

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    /**
     * Deleta ItemContratacao
     * @param int $id
     * @param bool $autoCommit
     * @return bool
     * @throws Exception
     */
    public function destroy(int $id, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $itemContratacao = $this->getById($id);
            $itemContratacao->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
