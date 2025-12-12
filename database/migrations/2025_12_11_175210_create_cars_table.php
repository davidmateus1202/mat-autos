<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained();
            $table->string('model');
            $table->integer('year');
            $table->string('vin')->unique()->nullable();
            $table->string('plate')->nullable();
            $table->string('color')->nullable();
            $table->decimal('purchase_price', 15, 2);
            $table->date('purchase_date');
            $table->enum('status', ['available', 'sold', 'reserved'])->default('available');
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->date('sale_date')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
