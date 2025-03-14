<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\UsuarioSetorContract;
use App\Databases\Models\UsuarioSetor;
use App\Databases\Models\VwSetorGestor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class UsuarioSetorRepository implements UsuarioSetorContract
{
    /**
     * Constructor
     * @param UsuarioSetor $usuarioSetor
     */
    public function __construct(private UsuarioSetor $usuarioSetor)
    {
    }

    /**
     * Buscar registro UsuarioSetor por Id
     * @param int $id
     * @return Model
     */
    public function getById(int $id): Model
    {
        return UsuarioSetor::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    /**
     * Busca todos registros de UsuarioSetor
     * @return Collection
     */
    public function getAll(): Collection
    {
        return UsuarioSetor::query()->get();
    }

    /**
     * Pagina com filtros as UsuarioSetor
     * @param array $pagination
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = VwSetorGestor::query();

        if (isset($pagination['codigo_setor'])) {
            $keyword = mb_strtolower($pagination['codigo_setor']);
            $query->whereRaw('lower(codigo_setor) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['numero_matricula'])) {
            $keyword = mb_strtolower($pagination['numero_matricula']);
            $query->whereRaw('lower(numero_matricula) like ?', ["%{$keyword}%"]);
        }
        if (isset($pagination['numero_matricula_gestor'])) {
            $keyword = mb_strtolower($pagination['numero_matricula_gestor']);
            $query->whereRaw('lower(numero_matricula_gestor) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'codigo_setor', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    /**
     * Cria um novo registro de UsuarioSetor
     * @param array $params
     * @param bool $autoCommit
     * @return bool
     * @throws Exception
     */
    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $usuarioSetor = new UsuarioSetor([
                'codigo_setor' => $params['codigo_setor'],
                'numero_matricula' => $params['numero_matricula'],
                'numero_matricula_gestor' => $params['numero_matricula_gestor']
            ]);
            $usuarioSetor->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    /**
     * Atualiza um registro existente de UsuarioSetor
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
            $usuarioSetor = $this->getById($id);
            $usuarioSetor->update($params);

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    /**
     * Deleta UsuarioSetor
     * @param int $id
     * @param bool $autoCommit
     * @return bool
     * @throws Exception
     */
    public function destroy(int $id, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $usuarioSetor = $this->getById($id);
            $usuarioSetor->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
