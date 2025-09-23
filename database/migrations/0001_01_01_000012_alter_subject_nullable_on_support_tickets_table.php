<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: ALTER COLUMN + DROP NOT NULL
            DB::statement("ALTER TABLE support_tickets ALTER COLUMN message DROP NOT NULL");
        } elseif ($driver === 'mysql') {
            // MySQL: MODIFY
            DB::statement("ALTER TABLE support_tickets MODIFY message TEXT NULL");
        } else {
            // Outros bancos: usar Schema builder (requer doctrine/dbal)
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->text('message')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE support_tickets ALTER COLUMN message SET NOT NULL");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE support_tickets MODIFY message TEXT NOT NULL");
        } else {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->text('message')->nullable(false)->change();
            });
        }
    }
};