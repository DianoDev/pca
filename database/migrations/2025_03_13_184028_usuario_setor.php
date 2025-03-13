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
        Schema::create('usuario_setor', function (Blueprint $table) {
            $table->unsignedBigInteger('codigo_setor');
            $table->unsignedBigInteger('numero_matricula');
            $table->unsignedBigInteger('numero_matricula_gestor');
            $table->timestamps();
            $table->softDeletes();
            // Chave primária composta
            $table->primary(['codigo_setor', 'numero_matricula']);

            // Índice para a chave estrangeira para scmpca_setor_pca
            $table->foreign('codigo_setor')
                ->references('codigo_setor')
                ->on('scmpca_setor_pca')
                ->onDelete('cascade');

            // Índice para o número de matrícula
            $table->index('numero_matricula');

            // Índice para o número de matrícula do gestor
            $table->index('numero_matricula_gestor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_setor');
    }
};
