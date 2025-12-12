<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarExpenseRequest;
use App\Models\Car;
use App\Models\CarExpense;
use App\Services\FinancialService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CarExpenseController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function store(StoreCarExpenseRequest $request, Car $car)
    {
        return DB::transaction(function () use ($request, $car) {
            $data = $request->validated();
            
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('expenses', 'public');
                $data['image_path'] = $path;
            }

            $expense = $car->expenses()->create($data);

            $this->financialService->recordCarExpense(
                $expense,
                $request->account_id
            );

            return response()->json($expense, 201);
        });
    }
}

