<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS fermer_planning_precedent');
    }

    public function down(): void
    {
        // Application code now manages planning intervals without database triggers.
    }
};
