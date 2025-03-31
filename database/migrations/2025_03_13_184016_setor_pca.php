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
        Schema::create('setor_pca', function (Blueprint $table) {
            $table->unsignedBigInteger('codigo_setor')->primary();
            $table->unsignedBigInteger('codigo_setor_pai')->nullable();
            $table->char('ativo', 1);
            $table->integer('hierarquia' );
            $table->char('tem_setor_filho', 1);
            $table->timestamps();
            $table->softDeletes();
            // Índice para o código setor pai
            $table->index('codigo_setor_pai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setor_pca');
    }
};
