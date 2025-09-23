<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->string('status')->default('aberto');
            $table->string('subject')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('order_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('support_tickets');
    }
};
