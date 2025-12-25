<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_badge', 20);
            $table->string('user_name', 100);
            $table->string('role', 20);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_badge')->references('BADGE')->on('user')->onDelete('cascade');
            $table->index('login_at');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
