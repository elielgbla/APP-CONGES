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
        Schema::create('employe', function (Blueprint $table) {
            // Informations d'identification de l'employé
            $table->string('matricule_employe', 20)->primary();
            $table->string('nom', 50);
            $table->string('prenom', 70);
            $table->date('date_naissance')->nullable();
            
            // Fait immuable : Seul le lieu de naissance reste rattaché directement
            $table->string('code_localite_naissance', 10)->nullable();
            
            // Sécurité & Compte de connexion
            $table->string('mot_de_passe', 255);
            $table->enum('statut_compte', ['actif', 'inactif', 'suspendu'])->default('actif');
            $table->string('email', 100)->unique()->nullable();
            $table->string('photo', 255)->nullable();
            
            // Compteurs RH
            $table->integer('total_conge_restant')->default(0);
            
            // Caractéristiques structurelles fixes
            $table->string('code_genre', 10);
            $table->string('code_categorie', 10);

            // Définition des contraintes de clés étrangères valides
            $table->foreign('code_localite_naissance')->references('code_localite')->on('localite')->onDelete('set null');
            $table->foreign('code_genre')->references('code_genre')->on('genre');
            $table->foreign('code_categorie')->references('code_categorie')->on('categorie');
            
            // Suppression des FK et colonnes (code_residence, code_poste, code_service) 
            // car elles sont gérées dynamiquement par les tables pivots/historiques.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employe');
    }
};