<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisement_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisementss_id')->constrained('advertisementss')->onDelete('cascade');
            $table->uuid('uid')->nullable(); // si quieres usar UUID para identificar imágenes en el frontend
            $table->string('image'); 
            $table->boolean('is_main')->default(false); 
            $table->json('crop_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisement_images');
    }
};
