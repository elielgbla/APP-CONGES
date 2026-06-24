<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journalisation', function (Blueprint $table) {
            $table->string('code_log', 20)->primary();
            $table->string('matricule_employe', 20)->nullable();
            $table->string('action', 100);
            $table->string('table_concernee', 50)->nullable();
            $table->timestamp('date_action')->useCurrent();
            $table->text('details')->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->foreign('matricule_employe')->references('matricule_employe')->on('employe')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journalisation');
    }
};