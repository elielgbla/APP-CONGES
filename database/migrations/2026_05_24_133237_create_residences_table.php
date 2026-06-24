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
        // On change Schema::table par Schema::create pour INITIALISER la table
        Schema::create('residence', function (Blueprint $table) {
            // Clé primaire de la table residence
            $table->id('code_residence'); 
            
            // Ajoute ici les autres colonnes de ta table si nécessaire
            // $table->string('libelle_residence');

            // On crée le matricule de l'employé en nullable
            $table->string('matricule_employe', 20)->nullable();

            // Clé étrangère reliée à la table employes
            $table->foreign('matricule_employe')
                  ->references('matricule_employe')
                  ->on('employes')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Plus besoin de cibler la colonne, on supprime carrément la table entière
        Schema::dropIfExists('residence');
    }
};