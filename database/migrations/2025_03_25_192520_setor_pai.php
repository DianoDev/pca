<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('setor_pca')->insert([
            'codigo_setor' => 10000,
            'codigo_setor_pai' => null,
            'ativo' => 'S',
            'hierarquia' => 1,
            'tem_setor_filho' => 'N',
            'created_at' => '2025-03-24 13:00:24',
            'updated_at' => '2025-03-24 13:00:24',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('setor_pca')
            ->where('codigo_setor', 10100)
            ->delete();
    }
};
