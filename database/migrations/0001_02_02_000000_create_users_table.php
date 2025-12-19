<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete();
            $table->string('full_name');
            $table->string('email', 120)->unique();
            $table->string('password', 255);
            $table->string('dni', 8)->unique();
            $table->string('phone', 9)->nullable();
            $table->string('locality', 150)->nullable();
            $table->string('whatsapp', 20)->nullable();       
            $table->string('call_phone', 20)->nullable();     
            $table->string('contact_email', 150)->nullable(); 
            $table->string('address', 200)->nullable();  
            $table->decimal('virtual_wallet', 10, 2)->default(0);   
            $table->boolean('is_active')->default(true);
            $table->boolean('privacy_policy_accepted')->default(false);
            $table->timestamp('privacy_policy_accepted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
