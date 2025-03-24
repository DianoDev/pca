<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\ItemProrrogacaoContract;
use App\Databases\Models\ItemProrrogacao;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class ItemProrrogacaoRepository implements ItemProrrogacaoContract
{
    /**
     * Constructor
     * @param ItemProrrogacao $itemProrrogacao
     */
    public function __construct(private ItemProrrogacao $itemProrrogacao)
    {
    }

    /**
     * Buscar registro ItemProrrogacao por Id
     * @param int $id
     * @return Model
     */
    public function getById(int $id): Model
    {
        return ItemProrrogacao::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    /**
     * Busca todos registros de ItemProrrogacao
     * @return Collection
     */
    public function getAll(): Collection
    {
        return ItemProrrogacao::query()->get();
    }

    /**
     * Pagina com filtros as ItemProrrogacao
     * @param array $pagination
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = ItemProrrogacao::query()->where('id_plano_contratacao','=', $pagination['id_plano_contratacao']);

        if (isset($pagination['objeto'])) {
            $keyword = mb_strtolower($pagination['objeto']);
            $query->whereRaw('lower(objeto) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['numero'])) {
            $keyword = mb_strtolower($pagination['numero']);
            $query->whereRaw('lower(numero) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['empresa'])) {
            $keyword = mb_strtolower($pagination['empresa']);
            $query->whereRaw('lower(empresa) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['cnpj'])) {
            $keyword = mb_strtolower($pagination['cnpj']);
            $query->whereRaw('lower(cnpj) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['valor_global'])) {
            $keyword = mb_strtolower($pagination['valor_global']);
            $query->whereRaw('lower(valor_global) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['termino_vigencia'])) {
            $keyword = mb_strtolower($pagination['termino_vigencia']);
            $query->whereRaw('lower(termino_vigencia) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'objeto', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    /**
     * Cria um novo registro de ItemProrrogacao
     * @param array $params
     * @param bool $autoCommit
     * @return bool
     * @throws Exception
     */
    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $itemProrrogacao = new ItemProrrogacao([
                'id_plano_contratacao' => $params['id_plano_contratacao'],
                'objeto' => $params['objeto'],
                'numero' => $params['numero'],
                'empresa' => $params['empresa'],
                'cnpj' => $params['cnpj'],
                'valor_global' => $params['valor_global'],
                'termino_vigencia' => $params['termino_vigencia'],
                'status' => 'E'
            ]);
            $itemProrrogacao->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    /**
     * Atualiza um registro existente de ItemProrrogacao
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
            $itemProrrogacao = $this->getById($id);
            $itemProrrogacao->update($params);

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    /**
     * Deleta ItemProrrogacao
     * @param int $id
     * @param bool $autoCommit
     * @return bool
     * @throws Exception
     */
    public function destroy(int $id, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $itemProrrogacao = $this->getById($id);
            $itemProrrogacao->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
