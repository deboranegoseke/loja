<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('sender_type', ['customer','store']);
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index(['support_ticket_id','created_at']);
            $table->index('sender_type');
            $table->index('user_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('support_messages');
    }
};
