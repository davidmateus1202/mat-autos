<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarExpense;
use App\Models\CarSale;
use App\Models\FinancialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary()
    {
        // 1. Available Cash (Sum of all account balances)
        // Since balance is computed, we sum initial + movements
        // Or iterate accounts. For performance, SQL aggregation is better.
        // Balance = initial + sum(movements.amount)
        $accounts = FinancialAccount::withSum('movements', 'amount')->get();
        $availableCash = $accounts->sum(function ($acc) {
            return $acc->initial_balance + $acc->movements_sum_amount;
        });

        // 2. Invested in Cars (Sum of purchase_price + expenses for AVAILABLE cars)
        // We can approximate this by taking all cars where status = available
        // Cost = purchase_price + expenses
        $invested = Car::where('status', 'available')
            ->withSum('expenses', 'amount')
            ->get()
            ->sum(function ($car) {
                return $car->purchase_price + $car->expenses_sum_amount;
            });

        // 3. Sold this month
        $startOfMonth = now()->startOfMonth();
        $soldCount = Car::where('status', 'sold')
            ->where('sold_at', '>=', $startOfMonth)
            ->count();

        // 4. Profits this month
        // Gross = Sale - Purchase
        // Net = Sale - (Purchase + Expenses)
        $salesThisMonth = Car::where('status', 'sold')
            ->where('sold_at', '>=', $startOfMonth)
            ->with(['expenses'])
            ->get();

        $grossProfit = $salesThisMonth->sum(fn($c) => $c->sale_price - $c->purchase_price);
        $netProfit = $salesThisMonth->sum(fn($c) => $c->sale_price - ($c->purchase_price + $c->expenses->sum('amount')));

        return response()->json([
            'available_cash' => $availableCash,
            'invested_assets' => $invested,
            'cars_sold_month' => $soldCount,
            'gross_profit_month' => $grossProfit,
            'net_profit_month' => $netProfit,
        ]);
    }

    public function salesByBrand()
    {
        $data = Car::where('status', 'sold')
            ->join('brands', 'cars.brand_id', '=', 'brands.id')
            ->select('brands.name', DB::raw('count(*) as total'))
            ->groupBy('brands.name')
            ->get();

        return response()->json($data);
    }

    public function monthlyStats(Request $request)
    {
        $months = $request->get('months', 12);
        // Logic to get last X months stats (sales count, expenses total, profit)
        // This is a bit complex for a single query, might need multiple or a complex union.
        // Simplified version:
        
        $stats = [];
        for ($i = 0; $i < $months; $i++) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            $label = $date->format('M Y');

            $sales = Car::where('status', 'sold')
                ->whereBetween('sold_at', [$monthStart, $monthEnd])
                ->count();

            $expenses = CarExpense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            // Profit calculation for the month
            $monthlySales = Car::where('status', 'sold')
                ->whereBetween('sold_at', [$monthStart, $monthEnd])
                ->with('expenses')
                ->get();
            
            $profit = $monthlySales->sum(fn($c) => $c->sale_price - ($c->purchase_price + $c->expenses->sum('amount')));

            $stats[] = [
                'label' => $label,
                'sales_count' => $sales,
                'expenses_amount' => $expenses,
                'net_profit' => $profit
            ];
        }

        return response()->json(array_reverse($stats));
    }
}

