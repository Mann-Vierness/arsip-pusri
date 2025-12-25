<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->string('badge', 50);
            $table->string('activity', 100);
            $table->text('details')->nullable();
            $table->timestamps();
            
            $table->index(['badge', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
