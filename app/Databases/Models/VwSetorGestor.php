<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VwSetorGestor extends Model
{
    protected $table = 'vw_setor_gestor';
    protected $keyType = 'string';
}
