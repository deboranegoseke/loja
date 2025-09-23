<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            // Adiciona apenas se não existir (evita erros em ambientes diferentes)
            if (! Schema::hasColumn('orders', 'customer_name')) {
                $t->string('customer_name')->nullable();
            }
            if (! Schema::hasColumn('orders', 'customer_email')) {
                $t->string('customer_email')->nullable();
            }
            if (! Schema::hasColumn('orders', 'pix_txid')) {
                $t->string('pix_txid', 35)->nullable();
            }
            if (! Schema::hasColumn('orders', 'pix_payload')) {
                $t->text('pix_payload')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            if (Schema::hasColumn('orders', 'pix_payload')) {
                $t->dropColumn('pix_payload');
            }
            if (Schema::hasColumn('orders', 'pix_txid')) {
                $t->dropColumn('pix_txid');
            }
            if (Schema::hasColumn('orders', 'customer_email')) {
                $t->dropColumn('customer_email');
            }
            if (Schema::hasColumn('orders', 'customer_name')) {
                $t->dropColumn('customer_name');
            }
        });
    }
};
