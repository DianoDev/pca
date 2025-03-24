<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemProrrogacao extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'item_prorrogacao';
    public string $sequence = 'item_prorrogacao_id_seq';
    protected $guarded = [];
}
