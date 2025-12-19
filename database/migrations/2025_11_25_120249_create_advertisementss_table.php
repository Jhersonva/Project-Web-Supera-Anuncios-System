<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('advertisementss', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_categories_id')->constrained('ad_categories');
            $table->foreignId('ad_subcategories_id')->constrained('ad_subcategories');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('department')->nullable();
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('contact_location')->nullable();
            $table->decimal('amount', 10, 2)->nullable(0);
            $table->boolean('amount_visible')->default(1);
            $table->unsignedInteger('days_active')->default(1);
            $table->dateTime('expires_at')->nullable();
            $table->boolean('published')->default(false);
            $table->unsignedTinyInteger('stars')->default(0); 
            $table->boolean('urgent_publication')->default(false); 
            $table->decimal('urgent_price', 10, 2)->nullable();
            $table->boolean('featured_publication')->default(false); 
            $table->decimal('featured_price', 10, 2)->nullable(); 
            $table->boolean('premiere_publication')->default(false);
            $table->decimal('premiere_price', 10, 2)->nullable();
            $table->boolean('semi_new_publication')->default(false);
            $table->decimal('semi_new_price', 10, 2)->nullable();
            $table->boolean('new_publication')->default(false);
            $table->decimal('new_price', 10, 2)->nullable();
            $table->boolean('available_publication')->default(false);
            $table->decimal('available_price', 10, 2)->nullable();
            $table->boolean('top_publication')->default(false);
            $table->decimal('top_price', 10, 2)->nullable();
            $table->enum('status', ['pendiente', 'publicado', 'rechazado', 'expirado'])->default('pendiente');
            $table->enum('receipt_type', ['boleta', 'factura', 'nota_venta'])->nullable();
            $table->string('dni')->nullable();
            $table->string('full_name')->nullable();
            $table->string('ruc')->nullable();
            $table->string('company_name')->nullable();
            $table->string('address')->nullable();
            $table->string('receipt_file')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('advertisementss');
    }
};