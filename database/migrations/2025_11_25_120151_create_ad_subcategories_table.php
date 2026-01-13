<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('ad_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_categories_id')->constrained('ad_categories')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 12, 2)->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->boolean('is_premiere')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_semi_new')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_available')->default(false);
            $table->boolean('is_top')->default(false);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('ad_subcategories');
    }
};