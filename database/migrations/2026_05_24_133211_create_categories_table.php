<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorie', function (Blueprint $table) {
            $table->string('code_categorie', 10)->primary();
            $table->string('libelle_categorie', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorie');
    }
};