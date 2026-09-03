<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->bigInteger('size_bytes')->default(0);
            $table->enum('type', ['backup', 'restore']);
            $table->enum('status', ['pending', 'running', 'success', 'failed']);
            $table->text('message')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};
