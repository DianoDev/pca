<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CicloContratacao extends Model
{
    use SoftDeletes;

    protected $table = 'ciclo_contratacao';

    protected $fillable = [
        'ano',
        'descricao',
        'data_inicio',
        'data_fim',
        'status'
    ];

    /**
     * Relacionamento: um ciclo tem muitos planos
     */
    public function planos()
    {
        return $this->hasMany(PlanoContratacao::class, 'id_ciclo');
    }

    /**
     * Recupera planos ativos deste ciclo
     */
    public function planosAtivos()
    {
        return $this->planos()->where('status', 'A');
    }

    /**
     * Recupera planos finalizados deste ciclo
     */
    public function planosFinalizados()
    {
        return $this->planos()->where('status', 'F');
    }
}
