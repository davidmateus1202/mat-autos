<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BankLoan;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankLoanController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index()
    {
        $loans = BankLoan::with('financialAccount')->get()->each(function ($loan) {
            $loan->append(['current_debt', 'available_credit', 'total_disbursed', 'total_principal_paid']);
        });
        return response()->json(['data' => $loans]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'financial_account_id' => 'nullable|exists:financial_accounts,id',
            'bank_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'term_months' => 'nullable|integer|min:1',
        ]);

        $validated['status'] = 'approved'; // Always created as approved
        $loan = BankLoan::create($validated);
        return response()->json($loan, 201);
    }

    public function show(BankLoan $loan)
    {
        return response()->json($loan->load('payments', 'disbursements')->append(['current_debt', 'available_credit']));
    }

    public function disburse(Request $request, BankLoan $loan)
    {
        // Allow disbursement if status is approved or disbursed (revolving)
        if (!in_array($loan->status, ['pending', 'approved', 'disbursed'])) {
            return response()->json(['message' => 'Loan cannot be disbursed'], 422);
        }

        $validated = $request->validate([
            'account_id' => 'required|exists:financial_accounts,id',
            'disbursed_at' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($loan, $validated) {
            $updateData = [
                'status' => 'disbursed',
                'disbursement_date' => $validated['disbursed_at'] ?? now(),
                'financial_account_id' => $validated['account_id'],
            ];

            $loan->update($updateData);

            // Pass the specific disbursed amount to the service
            $disbursedAmount = isset($validated['amount']) && $validated['amount'] > 0 
                ? $validated['amount'] 
                : $loan->available_credit; // Default to remaining available credit

            if ($disbursedAmount > $loan->available_credit) {
                 // Optional: Prevent over-disbursement, or allow it? 
                 // For now, let's allow it but maybe warn? Or strict check?
                 // Strict check seems safer for "Cupo" logic.
                 // throw new \Exception("Amount exceeds available credit");
            }

            $this->financialService->recordLoanDisbursement($loan, $disbursedAmount);

            return response()->json($loan);
        });
    }

    public function addPayment(Request $request, BankLoan $loan)
    {
        if ($loan->status !== 'disbursed') {
            return response()->json(['message' => 'Loan is not active'], 422);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'principal_amount' => 'required|numeric|min:0',
            'interest_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'account_id' => 'required|exists:financial_accounts,id', // Account to pay from
        ]);

        // Validate total
        if (abs($validated['amount'] - ($validated['principal_amount'] + $validated['interest_amount'])) > 0.01) {
             return response()->json(['message' => 'Principal + Interest must equal Amount'], 422);
        }

        return DB::transaction(function () use ($request, $loan, $validated) {
            $payment = $loan->payments()->create([
                'amount' => $validated['amount'],
                'principal_amount' => $validated['principal_amount'],
                'interest_amount' => $validated['interest_amount'],
                'payment_date' => $validated['payment_date'],
            ]);

            $this->financialService->recordLoanPayment($payment, $validated['account_id']);

            // Check if fully paid (optional logic, keeping simple for now)
            if ($loan->remaining_balance <= 0) {
                $loan->update(['status' => 'paid']);
            }

            return response()->json($payment, 201);
        });
    }
}

