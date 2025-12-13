<?php

namespace App\Services;

use App\Models\BankLoan;
use App\Models\BankLoanPayment;
use App\Models\Car;
use App\Models\CarExpense;
use App\Models\CarSale;
use App\Models\FinancialMovement;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    public function recordCarPurchase(Car $car, int $accountId)
    {
        return FinancialMovement::create([
            'financial_account_id' => $accountId,
            'type' => 'car_purchase',
            'amount' => -abs($car->purchase_price), // Debit
            'description' => "Purchase of {$car->brand->name} {$car->model} ({$car->year})",
            'movement_date' => $car->purchase_date,
            'movementable_id' => $car->id,
            'movementable_type' => Car::class,
        ]);
    }

    public function recordCarExpense(CarExpense $expense, int $accountId)
    {
        return FinancialMovement::create([
            'financial_account_id' => $accountId,
            'type' => 'car_expense',
            'amount' => -abs($expense->amount), // Debit
            'description' => "Expense for {$expense->car->model}: {$expense->concept}",
            'movement_date' => $expense->expense_date,
            'movementable_id' => $expense->id,
            'movementable_type' => CarExpense::class,
        ]);
    }

    public function recordCarSale(CarSale $sale, int $accountId)
    {
        return FinancialMovement::create([
            'financial_account_id' => $accountId,
            'type' => 'car_sale',
            'amount' => abs($sale->sale_price), // Credit
            'description' => "Sale of {$sale->car->brand->name} {$sale->car->model}",
            'movement_date' => $sale->sale_date,
            'movementable_id' => $sale->id,
            'movementable_type' => CarSale::class,
        ]);
    }

    public function recordLoanDisbursement(BankLoan $loan, ?float $amount = null)
    {
        if ($loan->status !== 'disbursed') {
            return null;
        }

        $disbursedAmount = $amount ?? $loan->amount;

        return FinancialMovement::create([
            'financial_account_id' => $loan->financial_account_id,
            'type' => 'loan_disbursement',
            'amount' => abs($disbursedAmount), // Credit
            'description' => "Loan disbursement: {$loan->bank_name}",
            'movement_date' => $loan->disbursement_date ?? now(),
            'movementable_id' => $loan->id,
            'movementable_type' => BankLoan::class,
        ]);
    }

    public function recordLoanPayment(BankLoanPayment $payment, int $accountId)
    {
        return FinancialMovement::create([
            'financial_account_id' => $accountId,
            'type' => 'loan_payment',
            'amount' => -abs($payment->amount), // Debit
            'description' => "Loan payment: {$payment->bankLoan->bank_name}",
            'movement_date' => $payment->payment_date,
            'movementable_id' => $payment->id,
            'movementable_type' => BankLoanPayment::class,
        ]);
    }

    public function recordManualMovement(int $accountId, float $amount, string $description, string $date)
    {
        return FinancialMovement::create([
            'financial_account_id' => $accountId,
            'type' => 'adjustment',
            'amount' => $amount,
            'description' => $description,
            'movement_date' => $date,
        ]);
    }
}
