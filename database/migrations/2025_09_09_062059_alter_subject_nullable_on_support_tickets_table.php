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
            // PostgreSQL: sem MODIFY. Usa ALTER COLUMN e, se precisar, corta para 255.
            DB::statement("
                ALTER TABLE support_tickets
                    ALTER COLUMN subject TYPE VARCHAR(255) USING LEFT(subject, 255),
                    ALTER COLUMN subject DROP NOT NULL
            ");
        } elseif ($driver === 'mysql') {
            // MySQL
            DB::statement("ALTER TABLE support_tickets MODIFY subject VARCHAR(255) NULL");
        } else {
            // Fallback por Schema Builder (requer doctrine/dbal se for usar change())
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->string('subject', 255)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE support_tickets
                    ALTER COLUMN subject SET NOT NULL
            ");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE support_tickets MODIFY subject VARCHAR(255) NOT NULL");
        } else {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->string('subject', 255)->nullable(false)->change();
            });
        }
    }
};
