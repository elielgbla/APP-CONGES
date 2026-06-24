<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poste', function (Blueprint $table) {
            $table->string('code_poste', 10)->primary();
            $table->string('libelle_poste', 50);
            $table->tinyInteger('est_responsable')->default(0);
            $table->tinyInteger('niveau_validation')->nullable();
            $table->tinyInteger('acces_journalisation')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poste');
    }
};