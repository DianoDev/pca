<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SetorPCA extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'setor_pca';
    protected $primaryKey = 'codigo_setor';
    public $incrementing = false;

    protected $fillable = [
        'codigo_setor',
        'codigo_setor_pai',
        'ativo',
        'tem_setor_filho',
    ];

    // Relacionamento com o setor pai
    public function pai()
    {
        return $this->belongsTo(SetorPca::class, 'codigo_setor_pai', 'codigo_setor');
    }

    // Relacionamento com os setores filhos
    public function filhos()
    {
        return $this->hasMany(SetorPca::class, 'codigo_setor_pai', 'codigo_setor')
            ->where('ativo', 'S');
    }
}
