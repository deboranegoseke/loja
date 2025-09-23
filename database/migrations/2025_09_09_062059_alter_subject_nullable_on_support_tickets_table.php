<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Sem precisar de doctrine/dbal usando SQL direto:
        DB::statement('ALTER TABLE support_tickets MODIFY subject VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE support_tickets MODIFY subject VARCHAR(255) NOT NULL');
    }
};
