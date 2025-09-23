<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('logradouro');
            $table->string('numero', 50);
            $table->string('complemento')->nullable();
            $table->string('bairro', 120);
            $table->string('cidade', 120);
            $table->char('estado', 2);
            $table->string('cep', 9);
            $table->timestamps();

            $table->unique('user_id');                 // addresses_user_id_unique
            $table->index(['estado','cidade']);        // addresses_estado_cidade_index
        });
    }
    public function down(): void {
        Schema::dropIfExists('addresses');
    }
};
