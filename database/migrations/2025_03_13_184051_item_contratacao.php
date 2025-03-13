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
        Schema::create('item_contratacao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_plano_contratacao');
            $table->text('descricao');
            $table->string('unidade_medida', 20)->nullable();
            $table->unsignedBigInteger('quantidade')->nullable();
            $table->float('valor_unitario_estimado')->nullable();
            $table->float('valor_total')->nullable();
            $table->date('data_desejada')->nullable();
            $table->char('classificacao', 2)->nullable();
            $table->char('status', 1);
            $table->timestamps();

            // Índice para a chave estrangeira para plano_contratacao
            $table->foreign('id_plano_contratacao')
                ->references('id')
                ->on('plano_contratacao')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_contratacao');
    }
};
