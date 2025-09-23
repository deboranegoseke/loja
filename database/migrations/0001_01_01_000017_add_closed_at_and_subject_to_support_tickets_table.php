<?PHP
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            // adiciona subject se não existir
            if (!Schema::hasColumn('support_tickets', 'subject')) {
                $table->string('subject')->nullable()->after('status');
            }
            // adiciona closed_at se não existir
            if (!Schema::hasColumn('support_tickets', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('subject');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('support_tickets', 'closed_at')) {
                $table->dropColumn('closed_at');
            }
            if (Schema::hasColumn('support_tickets', 'subject')) {
                $table->dropColumn('subject');
            }
        });
    }
};
