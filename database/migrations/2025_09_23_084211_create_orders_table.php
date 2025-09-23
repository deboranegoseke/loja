<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total', 10, 2)->default(0.00);
            $table->string('status')->default('novo');
            $table->string('fulfillment_status', 30)->default('aguardando');
            $table->string('tracking_code')->nullable();
            $table->timestamps();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('pix_txid', 35)->nullable();
            $table->text('pix_payload')->nullable();

            $table->index('user_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
