<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service', function (Blueprint $table) {
            $table->string('code_service', 15)->primary();
            $table->string('libelle_service', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service');
    }
};