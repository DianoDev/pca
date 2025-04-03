<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CicloHierarquia extends Model
{
    use SoftDeletes;

    protected $table = 'ciclo_hierarquia';

    protected $fillable = [
        'hierarquia',
        'mes_limite_cadastro',
        'dia_limite_cadastro',
    ];

}
