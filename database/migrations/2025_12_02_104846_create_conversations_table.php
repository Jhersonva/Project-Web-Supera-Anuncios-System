<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            // Usuario que envía el primer mensaje
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();

            // Dueño del anuncio
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();

            // Anuncio relacionado
            $table->foreignId('advertisement_id')->constrained('advertisementss')->cascadeOnDelete();

            $table->timestamps();

            // Evita duplicados por anuncio
            $table->unique(['sender_id', 'receiver_id', 'advertisement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
