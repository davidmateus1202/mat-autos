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
        Schema::create('financial_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_account_id')->constrained();
            $table->string('type'); // 'car_purchase', 'car_expense', 'car_sale', 'loan_disbursement', 'loan_payment', 'transfer', 'adjustment'
            $table->decimal('amount', 15, 2); // Positive for credit, Negative for debit
            $table->string('description')->nullable();
            $table->date('movement_date');
            $table->nullableMorphs('movementable'); // Polymorphic relation to Car, CarExpense, CarSale, BankLoan, BankLoanPayment
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_movements');
    }
};
