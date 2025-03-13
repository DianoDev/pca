<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
        CREATE OR REPLACE VIEW SCMPCA.VW_SETOR_GESTOR AS
       SELECT
	s.CODIGO_SETOR,
	s.NOME_SETOR_FORMATADO,
	s.responsavel,
	vsf.NOME_FUNCIONARIO,
	vsf.LOGON
FROM publico.VW_SIGP_SETOR_SECORP s
LEFT JOIN PUBLICO.VW_SIGP_FUNCIONARIO vsf ON (vsf.NUMERO_MATRICULA = s.RESPONSAVEL)
");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW SCMPCA.VW_SETOR_GESTOR");
    }
};
