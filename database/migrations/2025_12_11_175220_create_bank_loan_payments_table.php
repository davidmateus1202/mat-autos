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
        Schema::create('bank_loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_loan_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2); // Total payment
            $table->decimal('principal_amount', 15, 2); // Capital
            $table->decimal('interest_amount', 15, 2); // Interest
            $table->date('payment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_loan_payments');
    }
};
