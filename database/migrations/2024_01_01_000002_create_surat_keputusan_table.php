<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_keputusan', function (Blueprint $table) {
            $table->id();
            $table->string('NOMOR_SK', 100)->unique();
            $table->date('TANGGAL');
            $table->text('PERIHAL');
            $table->string('PENANDATANGAN', 100);
            $table->string('UNIT_KERJA', 100);
            $table->string('NAMA', 100);
            $table->string('USER', 50);
            $table->string('pdf_path')->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('approved_by', 50)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('USER')->references('BADGE')->on('user')->onDelete('cascade');
            $table->index(['TANGGAL', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_keputusan');
    }
};
