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
            $table->string('nombre');              
            $table->string('tipo')->nullable();     
            $table->string('numero')->nullable();   
            $table->string('cuenta')->nullable();   
            $table->string('cci')->nullable();      
            $table->string('qr')->nullable();       
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
