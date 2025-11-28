<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('values_fields_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisementss_id')->constrained('advertisementss')->onDelete('cascade');
            $table->foreignId('fields_subcategory_ads_id')->constrained('fields_subcategory_ads')->onDelete('cascade');
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('values_fields_ads');
    }
};
