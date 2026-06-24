<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('type_contact', function (Blueprint $table) {
            $table->string('code_type_contact', 10)->primary();
            $table->string('libelle_type_contact', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_contact');
    }
};