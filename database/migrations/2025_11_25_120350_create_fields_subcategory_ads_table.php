<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('fields_subcategory_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_subcategories_id')->constrained('ad_subcategories')->onDelete('cascade');
            $table->string('name');
            $table->string('type')->default('string'); 
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('fields_subcategory_ads');
    }
};