<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            if (!Schema::hasColumn('orders', 'fulfillment_status')) {
                // aguardando|separacao|em_transito|rota_entrega|entregue|problema|cancelado
                $t->string('fulfillment_status', 30)->default('aguardando')->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            if (Schema::hasColumn('orders', 'fulfillment_status')) {
                $t->dropColumn('fulfillment_status');
            }
        });
    }
};
