<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validation_permission', function (Blueprint $table) {
            $table->string('code_validation', 15)->primary();
            $table->string('code_demande', 15);
            $table->string('matricule_valideur', 20);
            $table->tinyInteger('niveau_validation');
            $table->enum('decision', ['valide', 'rejete']);
            $table->date('date_decision');
            $table->text('commentaire')->nullable();
            $table->string('signature', 255)->nullable();

            $table->unique(['code_demande', 'niveau_validation']);
            $table->foreign('code_demande')->references('code_demande')->on('demande')->onDelete('cascade');
            $table->foreign('matricule_valideur')->references('matricule_employe')->on('employe');
        });

        // Trigger : faire avancer le statut de la demande après validation
        DB::unprepared('
            CREATE TRIGGER avancer_statut_demande
            AFTER INSERT ON validation_permission
            FOR EACH ROW
            BEGIN
                IF NEW.decision = \'valide\' THEN
                    UPDATE demande SET statut_demande =
                        CASE NEW.niveau_validation
                            WHEN 1 THEN \'en_attente_n2\'
                            WHEN 2 THEN \'en_attente_n3\'
                            WHEN 3 THEN \'en_attente_n4\'
                            WHEN 4 THEN \'approuvee\'
                        END
                    WHERE code_demande = NEW.code_demande;
                ELSEIF NEW.decision = \'rejete\' THEN
                    UPDATE demande SET statut_demande = \'rejetee\'
                    WHERE code_demande = NEW.code_demande;
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS avancer_statut_demande');
        Schema::dropIfExists('validation_permission');
    }
};