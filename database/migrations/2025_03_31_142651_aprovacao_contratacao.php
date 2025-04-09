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
        Schema::create('aprovacao_contratacao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_plano_contratacao');
            $table->unsignedBigInteger('codigo_setor');
            $table->integer('numero_matricula_aprovacao');
            $table->integer('hierarquia_aprovacao')->nullable();
            $table->char('status', 1);
            $table->text('observacao')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // Índice para a chave estrangeira para plano_contratacao
            $table->foreign('id_plano_contratacao')
                ->references('id')
                ->on('plano_contratacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aprovacao_contratacao');
    }
};
