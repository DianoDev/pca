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
        Schema::create('item_prorrogacao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_plano_contratacao');
            $table->unsignedBigInteger('id_contrato')->nullable();
            $table->string('objeto');
            $table->string('numero', 20)->nullable();
            $table->string('empresa')->nullable();
            $table->string('cnpj')->nullable();
            $table->float('valor_global')->nullable();
            $table->date('termino_vigencia')->nullable();
            $table->char('status', 1);
            $table->timestamps();
            $table->softDeletes();
            // Índice para a chave estrangeira para plano_contratacao
            $table->foreign('id_plano_contratacao')
                ->references('id')
                ->on('plano_contratacao')
                ->onDelete('cascade');
            // Índice para a chave estrangeira para plano_contratacao
            $table->foreign('id_contrato')
                ->references('id')
                ->on('cori.contrato')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_prorrogacao');
    }
};
