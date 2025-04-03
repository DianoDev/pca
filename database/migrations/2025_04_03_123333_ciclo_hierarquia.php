<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 2. Criar tabela para definição de prazos por hierarquia
        Schema::create('ciclo_hierarquia', function (Blueprint $table) {
            $table->id();
            $table->integer('hierarquia'); // 1, 2, 3 ou 4
            $table->integer('mes_limite_cadastro'); // 1 a 12 (janeiro a dezembro)
            $table->integer('dia_limite_cadastro'); // 1 a 31
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['hierarquia']);
        });

        DB::table('ciclo_hierarquia')->insert([
            'hierarquia' => 4,
            'mes_limite_cadastro' => 4,  // Abril
            'dia_limite_cadastro' => 30, // Dia 30
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Hierarquia 3 - Cadastra até 31 de março
        DB::table('ciclo_hierarquia')->insert([
            'hierarquia' => 3,
            'mes_limite_cadastro' => 3,  // Março
            'dia_limite_cadastro' => 31, // Dia 31
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Hierarquia 2 - Cadastra até 28 de fevereiro
        DB::table('ciclo_hierarquia')->insert([
            'hierarquia' => 2,
            'mes_limite_cadastro' => 2,  // Fevereiro
            'dia_limite_cadastro' => 28, // Dia 28
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Hierarquia 1 (topo) - Cadastra até 31 de janeiro
        DB::table('ciclo_hierarquia')->insert([
            'hierarquia' => 1,
            'mes_limite_cadastro' => 1,
            'dia_limite_cadastro' => 31,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciclo_hierarquia');

    }
};
