<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('ad_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
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
        Schema::dropIfExists('ad_categories');
    }
};
