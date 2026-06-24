<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_poste', function (Blueprint $table) {
            $table->string('code_historique', 15)->primary();
            $table->string('matricule_employe', 20);
            $table->string('code_poste', 10);
            $table->string('code_service', 15);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();

            $table->foreign('matricule_employe')->references('matricule_employe')->on('employe')->onDelete('cascade');
            $table->foreign('code_poste')->references('code_poste')->on('poste');
            $table->foreign('code_service')->references('code_service')->on('service');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_poste');
    }
};