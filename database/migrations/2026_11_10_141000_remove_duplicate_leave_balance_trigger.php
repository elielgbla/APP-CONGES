<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS maj_conge_apres_approbation');
    }

    public function down(): void
    {
        // Leave balances are updated by the approval workflow.
    }
};
