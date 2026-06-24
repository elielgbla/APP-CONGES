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
            $table->date('date_fin')->nullable(); // Nullable car géré par ton trigger au prochain planning
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->unsignedTinyInteger('jour_repos_1'); // Requis (Formulaire create)
            $table->unsignedTinyInteger('jour_repos_2')->nullable(); // Optionnel

            $table->foreign('matricule_employe')->references('matricule_employe')->on('employe')->onDelete('cascade');
        });

        // Trigger : fermer automatiquement le planning précédent
DB::unprepared('
    CREATE TRIGGER fermer_planning_precedent
    BEFORE INSERT ON planning_journalier_employe
    FOR EACH ROW
    BEGIN
        UPDATE planning_journalier_employe
        SET date_fin = DATE_SUB(NEW.date_debut, INTERVAL 1 DAY)
        WHERE matricule_employe = NEW.matricule_employe
        AND date_fin IS NULL;
    END
');
    }

    public function down(): void
    {   
        // On supprime d'abord le trigger, puis directement la table entière
        DB::unprepared('DROP TRIGGER IF EXISTS fermer_planning_precedent');
        Schema::dropIfExists('planning_journalier_employe');
    }
};