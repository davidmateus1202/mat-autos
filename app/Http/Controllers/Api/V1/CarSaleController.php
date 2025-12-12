<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarSaleRequest;
use App\Models\Car;
use App\Services\FinancialService;
use Illuminate\Support\Facades\DB;

class CarSaleController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function store(StoreCarSaleRequest $request, Car $car)
    {
        if ($car->status !== 'available') {
            return response()->json(['message' => 'Car is not available for sale'], 422);
        }

        return DB::transaction(function () use ($request, $car) {
            $sale = $car->sale()->create($request->validated());

            $car->update([
                'status' => 'sold',
                'sale_price' => $sale->sale_price,
                'sale_date' => $sale->sale_date,
                'sold_at' => now(),
            ]);

            $this->financialService->recordCarSale(
                $sale,
                $request->account_id
            );

            return response()->json($sale, 201);
        });
    }
}

