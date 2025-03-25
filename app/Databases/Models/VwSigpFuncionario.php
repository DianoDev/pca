<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VwSigpFuncionario extends Model
{
    protected $table = 'publico.vw_sigp_funcionario';
    protected $keyType = 'string';
}
