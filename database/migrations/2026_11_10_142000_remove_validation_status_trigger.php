<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS avancer_statut_demande');
    }

    public function down(): void
    {
        // Status transitions are managed by the application workflow.
    }
};
