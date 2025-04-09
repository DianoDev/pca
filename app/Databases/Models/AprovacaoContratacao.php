<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AprovacaoContratacao extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aprovacao_contratacao';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_plano_contratacao',
        'codigo_setor',
        'numero_matricula_aprovacao',
        'status',
        'observacao',
    ];

    /**
     * Relacionamento: um ciclo tem muitos planos
     */
    public function plano()
    {
        return $this->belongsTo(PlanoContratacao::class, 'id_plano_contratacao');
    }

    public function usuario()
    {
        return $this->belongsTo(VwSigpFuncionario::class, 'numero_matricula_aprovacao', 'numero_matricula')
            ->select(['numero_matricula', 'nome_funcionario']);
    }
    public function nome_setor()
    {
        return $this->belongsTo(VwSigpSetorSecorp::class, 'codigo_setor', 'codigo_setor')
            ->select(['codigo_setor', 'nome_setor_formatado']);
    }
}
