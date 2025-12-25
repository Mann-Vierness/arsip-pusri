<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->string('BADGE', 50)->primary();
            $table->string('Nama', 100);
            $table->string('Password');
            $table->enum('ROLE', ['admin', 'user'])->default('user');
            $table->string('Departemen', 100)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
