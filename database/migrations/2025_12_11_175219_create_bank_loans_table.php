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
        Schema::create('bank_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_account_id')->constrained(); // Account receiving the loan
            $table->string('bank_name');
            $table->decimal('amount', 15, 2);
            $table->decimal('interest_rate', 5, 2)->nullable(); // Annual or monthly
            $table->date('start_date');
            $table->integer('term_months')->nullable();
            $table->enum('status', ['pending', 'approved', 'disbursed', 'paid', 'cancelled'])->default('pending');
            $table->date('disbursement_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_loans');
    }
};
