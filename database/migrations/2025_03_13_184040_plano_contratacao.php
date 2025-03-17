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
        Schema::create('plano_contratacao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('codigo_setor');
            $table->unsignedBigInteger('numero_matricula_gestor');
            $table->string('matricula', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->char('status', 1);
            $table->float('valor_total')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // Índice para a chave estrangeira para scmpca_setor_pca
            $table->foreign('codigo_setor')
                ->references('codigo_setor')
                ->on('setor_pca')
                ->onDelete('cascade');

            // Índice para o número de matrícula do gestor
            $table->index('numero_matricula_gestor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plano_contratacao');
    }
};
