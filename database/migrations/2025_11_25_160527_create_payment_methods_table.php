<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name_method')->unique();              
            $table->string('type')->nullable(); 
            $table->string('logo')->nullable();  
            $table->string('holder_name')->nullable();     
            $table->string('cell_phone_number')->nullable();   
            $table->string('account_number')->nullable();   
            $table->string('cci')->nullable();      
            $table->string('qr')->nullable();       
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
