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
        Schema::create('car_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->decimal('sale_price', 15, 2);
            $table->date('sale_date');
            $table->string('customer_name')->nullable();
            $table->string('customer_document')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_sales');
    }
};
