<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('logradouro', 255);
            $table->string('numero', 50);
            $table->string('complemento', 255)->nullable();
            $table->string('bairro', 120);
            $table->string('cidade', 120);
            $table->char('estado', 2);           // UF
            $table->string('cep', 9);            // formato 00000-000 (aceita sem hífen também)

            $table->timestamps();

            $table->index(['estado', 'cidade']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
