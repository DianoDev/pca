<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanoContratacao extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'plano_contratacao';
    public string $sequence = 'plano_contratacao_id_seq';
    protected $guarded = [];
}
