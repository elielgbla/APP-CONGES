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
        Schema::create('residence', function (Blueprint $table) {
            // Clé primaire auto-incrémentée par MySQL
            $table->id('code_residence');

            // Matricule de l'employé
            $table->string('matricule_employe', 20)->nullable();

            // Code de la localité
            $table->string('code_localite', 50)->nullable();

            // Adresse textuelle
            $table->string('adresse', 100)->nullable();

            // Clé étrangère vers la table 'employe'
            $table->foreign('matricule_employe')
                  ->references('matricule_employe')
                  ->on('employe')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Clé étrangère vers la table 'localite'
            $table->foreign('code_localite')
                  ->references('code_localite')
                  ->on('localite')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residence');
    }
};