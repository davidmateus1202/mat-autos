<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BankLoan;
use App\Models\Car;
use App\Models\CarExpense;
use App\Models\FinancialAccount;
use App\Models\FinancialMovement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary()
    {
        $accounts = FinancialAccount::withSum('movements', 'amount')->get();
        $availableCash = $accounts->sum(function ($account) {
            return (float) $account->initial_balance + (float) ($account->movements_sum_amount ?? 0);
        });

        $startOfMonth = now()->startOfMonth();

        $availableCars = Car::where('status', 'available')
            ->with(['brand', 'expenses'])
            ->get()
            ->values();

        $soldCars = Car::where('status', 'sold')
            ->with(['brand', 'expenses'])
            ->get()
            ->values();

        $soldThisMonth = $soldCars
            ->filter(fn($car) => $car->sold_at && $car->sold_at->gte($startOfMonth))
            ->values();

        $invested = $availableCars->sum(fn($car) => $this->carTotalCost($car));

        $estimatedInventoryValue = $availableCars->sum(fn($car) => $this->inventoryEstimatedValue($car));
        $potentialInventoryProfit = $availableCars->sum(fn($car) => $this->inventoryPotentialProfit($car));

        $soldCount = $soldThisMonth->count();
        $soldCarsTotal = $soldCars->count();
        $reservedCars = Car::where('status', 'reserved')->count();

        $salesRevenue = $soldThisMonth->sum(fn($car) => (float) ($car->sale_price ?? 0));
        $grossProfit = $soldThisMonth->sum(fn($car) => (float) ($car->sale_price ?? 0) - (float) $car->purchase_price);
        $netProfit = $soldThisMonth->sum(fn($car) => $this->carNetProfit($car));
        $averageSaleTicket = $soldCount > 0 ? $salesRevenue / $soldCount : 0;

        $expensesMonth = (float) CarExpense::where('expense_date', '>=', $startOfMonth)->sum('amount');

        $movementsThisMonth = FinancialMovement::where('movement_date', '>=', $startOfMonth)->get();
        $inflowsMonth = (float) $movementsThisMonth
            ->filter(fn($movement) => (float) $movement->amount > 0)
            ->sum('amount');
        $outflowsMonth = abs((float) $movementsThisMonth
            ->filter(fn($movement) => (float) $movement->amount < 0)
            ->sum('amount'));

        $loans = BankLoan::withSum('payments as principal_paid_sum', 'principal_amount')
            ->withSum('disbursements as disbursed_sum', 'amount')
            ->get();

        $currentDebt = $loans->sum(fn($loan) => $this->loanCurrentDebt($loan));
        $availableCredit = $loans->sum(fn($loan) => max(0, (float) $loan->amount - $this->loanCurrentDebt($loan)));
        $activeLoans = $loans->filter(fn($loan) => $this->loanCurrentDebt($loan) > 0)->count();

        $topInventory = $availableCars
            ->map(function ($car) {
                $estimatedPrice = (float) ($car->estimated_price ?? 0);

                return [
                    'id' => $car->id,
                    'label' => $this->carLabel($car),
                    'year' => $car->year,
                    'plate' => $car->plate,
                    'total_cost' => $this->toMoney($this->carTotalCost($car)),
                    'estimated_price' => $this->toMoney($estimatedPrice),
                    'potential_profit' => $this->toMoney($this->inventoryPotentialProfit($car)),
                ];
            })
            ->sortByDesc(fn($car) => $car['estimated_price'] > 0 ? $car['potential_profit'] : $car['total_cost'])
            ->take(4)
            ->values()
            ->all();

        $recentSales = $soldCars
            ->sortByDesc('sold_at')
            ->take(4)
            ->map(function ($car) {
                return [
                    'id' => $car->id,
                    'label' => $this->carLabel($car),
                    'sold_at' => optional($car->sold_at)->toDateString(),
                    'sale_price' => $this->toMoney((float) ($car->sale_price ?? 0)),
                    'net_profit' => $this->toMoney($this->carNetProfit($car)),
                ];
            })
            ->values()
            ->all();

        $recentExpenses = CarExpense::with(['car.brand'])
            ->orderByDesc('expense_date')
            ->take(4)
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'car_label' => $expense->car ? $this->carLabel($expense->car) : 'Vehículo',
                    'concept' => $expense->concept,
                    'expense_date' => optional($expense->expense_date)->toDateString(),
                    'amount' => $this->toMoney((float) $expense->amount),
                ];
            })
            ->values()
            ->all();

        $statusBreakdown = [
            ['key' => 'available', 'label' => 'Disponibles', 'count' => $availableCars->count()],
            ['key' => 'sold', 'label' => 'Vendidos', 'count' => $soldCarsTotal],
            ['key' => 'reserved', 'label' => 'Apartados', 'count' => $reservedCars],
        ];

        return response()->json([
            'available_cash' => $this->toMoney($availableCash),
            'invested_assets' => $this->toMoney($invested),
            'cars_sold_month' => $soldCount,
            'gross_profit_month' => $this->toMoney($grossProfit),
            'net_profit_month' => $this->toMoney($netProfit),
            'expenses_month' => $this->toMoney($expensesMonth),
            'inflows_month' => $this->toMoney($inflowsMonth),
            'outflows_month' => $this->toMoney($outflowsMonth),
            'sales_revenue_month' => $this->toMoney($salesRevenue),
            'average_sale_ticket_month' => $this->toMoney($averageSaleTicket),
            'available_cars' => $availableCars->count(),
            'sold_cars_total' => $soldCarsTotal,
            'reserved_cars' => $reservedCars,
            'estimated_inventory_value' => $this->toMoney($estimatedInventoryValue),
            'potential_inventory_profit' => $this->toMoney($potentialInventoryProfit),
            'active_loans' => $activeLoans,
            'current_debt' => $this->toMoney($currentDebt),
            'available_credit' => $this->toMoney($availableCredit),
            'status_breakdown' => $statusBreakdown,
            'top_inventory' => $topInventory,
            'recent_sales' => $recentSales,
            'recent_expenses' => $recentExpenses,
        ]);
    }

    public function salesByBrand()
    {
        $data = Car::where('status', 'sold')
            ->with(['brand', 'expenses'])
            ->get()
            ->groupBy(fn($car) => $car->brand?->name ?? 'Sin marca')
            ->map(function ($cars, $brand) {
                return [
                    'name' => $brand,
                    'total' => $cars->count(),
                    'revenue' => $this->toMoney($cars->sum(fn($car) => (float) ($car->sale_price ?? 0))),
                    'net_profit' => $this->toMoney($cars->sum(fn($car) => $this->carNetProfit($car))),
                ];
            })
            ->sortByDesc('revenue')
            ->values()
            ->all();

        return response()->json($data);
    }

    public function monthlyStats(Request $request)
    {
        $months = min(max((int) $request->get('months', 8), 1), 12);
        $stats = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            $label = ucfirst($date->locale('es')->translatedFormat('M'));

            $monthlySales = Car::where('status', 'sold')
                ->whereBetween('sold_at', [$monthStart, $monthEnd])
                ->with('expenses')
                ->get();

            $salesCount = $monthlySales->count();
            $salesAmount = $monthlySales->sum(fn($car) => (float) ($car->sale_price ?? 0));

            $expenses = CarExpense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $grossProfit = $monthlySales->sum(fn($car) => (float) ($car->sale_price ?? 0) - (float) $car->purchase_price);
            $profit = $monthlySales->sum(fn($car) => $this->carNetProfit($car));

            $stats[] = [
                'label' => $label,
                'month' => $date->format('Y-m'),
                'sales_count' => $salesCount,
                'sales_amount' => $this->toMoney($salesAmount),
                'expenses_amount' => $this->toMoney((float) $expenses),
                'gross_profit' => $this->toMoney($grossProfit),
                'net_profit' => $this->toMoney($profit),
            ];
        }

        return response()->json($stats);
    }

    private function carLabel(Car $car): string
    {
        return trim(($car->brand?->name ? $car->brand->name . ' ' : '') . $car->model);
    }

    private function carTotalCost(Car $car): float
    {
        return (float) $car->purchase_price + (float) $car->expenses->sum('amount');
    }

    private function carNetProfit(Car $car): float
    {
        return (float) ($car->sale_price ?? 0) - $this->carTotalCost($car);
    }

    private function inventoryEstimatedValue(Car $car): float
    {
        $estimatedPrice = (float) ($car->estimated_price ?? 0);

        return $estimatedPrice > 0 ? $estimatedPrice : $this->carTotalCost($car);
    }

    private function inventoryPotentialProfit(Car $car): float
    {
        $estimatedPrice = (float) ($car->estimated_price ?? 0);

        return $estimatedPrice > 0 ? $estimatedPrice - $this->carTotalCost($car) : 0;
    }

    private function loanCurrentDebt(BankLoan $loan): float
    {
        return max(0, (float) ($loan->disbursed_sum ?? 0) - (float) ($loan->principal_paid_sum ?? 0));
    }

    private function toMoney(float $value): float
    {
        return round($value, 2);
    }
}

