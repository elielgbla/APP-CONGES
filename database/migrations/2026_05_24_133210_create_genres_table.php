<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genre', function (Blueprint $table) {
            $table->string('code_genre', 10)->primary();
            $table->string('libelle_genre', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genre');
    }
};