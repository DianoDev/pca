<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemContratacao extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'item_contratacao';
    public string $sequence = 'item_contratacao_id_seq';
    protected $guarded = [];
}
