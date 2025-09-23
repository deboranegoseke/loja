<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                     // dump: name varchar(255) NOT NULL
            $table->string('email')->unique();                           // dump: email varchar(255) NOT NULL
            $table->string('role', 20)->default('cliente')->index();     // dump: enum('cliente','adm','gerente') default 'cliente'
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('users');
    }
};
