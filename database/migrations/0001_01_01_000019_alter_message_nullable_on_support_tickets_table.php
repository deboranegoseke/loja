<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // No MySQL (XAMPP)
        DB::statement("ALTER TABLE support_tickets MODIFY message TEXT NULL");
    }

    public function down(): void
    {
        // Reverte para NOT NULL
        DB::statement("ALTER TABLE support_tickets MODIFY message TEXT NOT NULL");
    }
};
