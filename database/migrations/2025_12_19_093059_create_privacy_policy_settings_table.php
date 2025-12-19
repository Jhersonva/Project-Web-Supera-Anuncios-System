<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('privacy_policy_settings', function (Blueprint $table) {
            $table->id();
            $table->longText('privacy_text'); 
            $table->boolean('contains_explicit_content')->default(true);
            $table->boolean('requires_adult')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privacy_policy_settings');
    }
};
