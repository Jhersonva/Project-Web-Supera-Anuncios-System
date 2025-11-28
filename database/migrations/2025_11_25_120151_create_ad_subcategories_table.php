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
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('ad_subcategories');
    }
};