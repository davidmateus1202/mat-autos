<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Services\FinancialService;
use Illuminate\Http\Request;

class FinancialAccountController extends Controller
{
    public function index()
    {
        // Append computed balance
        $accounts = FinancialAccount::all()->each(function ($account) {
            $account->append('balance');
        });
        return response()->json([
            'data' => $accounts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        $account = FinancialAccount::create($validated);
        return response()->json($account, 201);
    }

    public function show(FinancialAccount $account)
    {
        return response()->json($account->append('balance')->load('movements'));
    }

    public function update(Request $request, FinancialAccount $account)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
        ]);

        $account->update($validated);
        return response()->json($account);
    }

    public function adjustBalance(Request $request, FinancialAccount $account, FinancialService $financialService)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $financialService->recordManualMovement(
            $account->id,
            $validated['amount'],
            $validated['description'],
            $validated['date']
        );

        return response()->json($account->append('balance')->load('movements'));
    }

    public function destroy(FinancialAccount $account)
    {
        if ($account->movements()->exists()) {
            return response()->json(['message' => 'Cannot delete account with movements'], 422);
        }
        $account->delete();
        return response()->json(null, 204);
    }
}

