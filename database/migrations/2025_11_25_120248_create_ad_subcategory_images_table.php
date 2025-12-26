<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_subcategory_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_subcategory_id')
                ->constrained('ad_subcategories')
                ->onDelete('cascade');

            $table->string('image');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_subcategory_images');
    }
};
