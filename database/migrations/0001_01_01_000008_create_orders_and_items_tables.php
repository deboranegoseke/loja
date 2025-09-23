<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $t->string('status')->default('pending');
                $t->decimal('total', 10, 2)->default(0);
                $t->string('pix_txid', 35)->nullable();
                $t->text('pix_payload')->nullable();
                $t->string('customer_name')->nullable();
                $t->string('customer_email')->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $t) {
                $t->id();
                $t->foreignId('order_id')->constrained()->cascadeOnDelete();
                $t->foreignId('product_id')->constrained()->cascadeOnDelete();
                $t->unsignedInteger('quantity');
                $t->decimal('unit_price', 10, 2);
                $t->decimal('total', 10, 2);
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
