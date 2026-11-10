<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demande', function (Blueprint $table) {
            $table->string('code_demande', 15)->primary();
            $table->date('date_demande');
            $table->date('date_debut_prevu');
            $table->date('date_fin_prevu');
            $table->text('motif')->nullable();
            $table->enum('statut_demande', [
                'en_attente_n1',
                'en_attente_n2',
                'en_attente_n3',
                'en_attente_n4',
                'approuvee',
                'rejetee'
            ])->default('en_attente_n1');
            $table->string('matricule_employe', 20);
            $table->string('code_type_permission', 10);

            $table->foreign('matricule_employe')->references('matricule_employe')->on('employe')->onDelete('cascade');
            $table->foreign('code_type_permission')->references('code_type_permission')->on('type_permission');
        });

    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS maj_conge_apres_approbation');
        Schema::dropIfExists('demande');
    }
};