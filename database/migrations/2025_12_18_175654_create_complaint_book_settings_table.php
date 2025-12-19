<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('complaint_book_settings', function (Blueprint $table) {

            $table->id();
            $table->string('business_name');
            $table->string('ruc', 11)->nullable();
            $table->string('address')->nullable();
            $table->text('legal_text');
            $table->string('notification_email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_book_settings');
    }
};