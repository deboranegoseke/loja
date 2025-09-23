<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Cria a coluna subject se não existir
        if (! Schema::hasColumn('support_tickets', 'subject')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->string('subject')->nullable()->after('status');
            });
        }

        // Cria a coluna closed_at se não existir
        if (! Schema::hasColumn('support_tickets', 'closed_at')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->timestamp('closed_at')->nullable()->after('subject');
            });
        }

        // Remove a coluna antiga message se ainda existir
        if (Schema::hasColumn('support_tickets', 'message')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->dropColumn('message');
            });
        }
    }

    public function down(): void
    {
        // Recria a coluna message ao reverter
        if (! Schema::hasColumn('support_tickets', 'message')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->text('message')->nullable()->after('order_id');
            });
        }

        // Remove closed_at ao reverter
        if (Schema::hasColumn('support_tickets', 'closed_at')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->dropColumn('closed_at');
            });
        }

        // Remove subject ao reverter
        if (Schema::hasColumn('support_tickets', 'subject')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->dropColumn('subject');
            });
        }
    }
};
