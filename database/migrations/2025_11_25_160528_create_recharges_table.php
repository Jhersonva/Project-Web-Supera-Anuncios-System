<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['yape', 'plin', 'bcp', 'interbank']);
            $table->string('img_cap_pago', 200)->nullable();
            $table->enum('status', ['pendiente', 'aceptado', 'rechazado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recharges');
    }
};
