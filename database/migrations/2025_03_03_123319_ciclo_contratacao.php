<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Criar tabela de ciclo de contratação
        Schema::create('ciclo_contratacao', function (Blueprint $table) {
            $table->id();
            $table->integer('ano');
            $table->string('descricao');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->char('status', 1); // A = Ativo, F = Finalizado, C = Cancelado
            $table->timestamps();
            $table->softDeletes();


            $table->index('ano');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Remover a coluna id_ciclo da tabela plano_contratacao
        Schema::table('plano_contratacao', function (Blueprint $table) {
            $table->dropForeign(['id_ciclo']);
            $table->dropColumn('id_ciclo');
        });


        // 3. Remover a tabela ciclo_contratacao
        Schema::dropIfExists('ciclo_contratacao');
    }
};
