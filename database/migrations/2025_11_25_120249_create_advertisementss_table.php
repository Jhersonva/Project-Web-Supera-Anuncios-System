<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('advertisementss', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_categories_id')->constrained('ad_categories');
            $table->foreignId('ad_subcategories_id')->constrained('ad_subcategories');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('contact_location')->nullable();
            $table->decimal('amount', 10, 2)->nullable(0);
            $table->boolean('amount_visible')->default(1);
            $table->unsignedInteger('days_active')->default(1);
            $table->dateTime('expires_at')->nullable();
            $table->boolean('published')->default(false);
            $table->unsignedTinyInteger('stars')->default(0); 
            $table->boolean('urgent_publication')->default(false); 
            $table->decimal('urgent_price', 10, 2)->nullable();
            $table->enum('status', ['pendiente', 'publicado', 'rechazado', 'expirado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('advertisementss');
    }
};