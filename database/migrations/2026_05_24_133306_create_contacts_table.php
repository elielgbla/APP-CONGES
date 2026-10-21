<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact', function (Blueprint $table) {
            $table->string('code_contact', 10)->primary();
            $table->string('numero_contact', (50);
            $table->string('matricule_employe', 20);
            $table->string('code_type_contact', 10);

            $table->foreign('matricule_employe')->references('matricule_employe')->on('employe')->onDelete('cascade');
            $table->foreign('code_type_contact')->references('code_type_contact')->on('type_contact');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact');
    }
};