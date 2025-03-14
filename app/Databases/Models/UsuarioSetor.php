<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioSetor extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'usuario_setor';
    public string $sequence = 'usuario_setor_id_seq';
    protected $guarded = [];
}
