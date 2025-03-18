<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanoContratacao extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'plano_contratacao';
    public string $sequence = 'plano_contratacao_id_seq';
    protected $guarded = [];

    public function setor(): BelongsTo
    {
        return $this->belongsTo(SetorPCA::class, 'codigo_setor', 'codigo_setor');
    }

    public function gestor(): BelongsTo
    {
        return $this->belongsTo(VwSetorGestor::class, 'codigo_setor', 'codigo_setor');
    }
}
