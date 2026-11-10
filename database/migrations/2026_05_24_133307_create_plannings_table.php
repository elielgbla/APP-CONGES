<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planning_journalier_employe', function (Blueprint $table) {
            $table->string('code_planning', 15)->primary();
            $table->string('matricule_employe', 20);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->unsignedTinyInteger('jour_repos_1');
            $table->unsignedTinyInteger('jour_repos_2')->nullable();

            $table->foreign('matricule_employe')->references('matricule_employe')->on('employe')->onDelete('cascade');
        });
    }

    public function down(): void
    {   
        // On supprime d'abord le trigger, puis directement la table entière
        DB::unprepared('DROP TRIGGER IF EXISTS fermer_planning_precedent');
        Schema::dropIfExists('planning_journalier_employe');
    }
};