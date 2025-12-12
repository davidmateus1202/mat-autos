<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarRequest;
use App\Models\Car;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        $query = Car::with(['brand', 'expenses', 'sale', 'images']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('model', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%")
                  ->orWhere('plate', 'like', "%{$search}%");
            });
        }

        $paginated = $query->latest()->paginate(10);
        $items = collect($paginated->items())->map(function ($car) {
            $firstImage = $car->images->first();
            $car->image_url = $firstImage ? asset('storage/' . $firstImage->path) : null;
            return $car;
        });
        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function store(StoreCarRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $car = Car::create($request->validated());

            $this->financialService->recordCarPurchase(
                $car,
                $request->account_id
            );

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('cars', 'public');
                    $car->images()->create(['path' => $path]);
                }
            }

            return response()->json($car->load(['brand', 'images']), 201);
        });
    }

    public function show(Car $car)
    {
        return response()->json($car->load(['brand', 'expenses', 'sale', 'financialMovements', 'images']));
    }

    public function update(Request $request, Car $car)
    {
        // Allow updating basic and pricing fields
        $validated = $request->validate([
            'model' => 'sometimes|string',
            'year' => 'sometimes|integer',
            'vin' => 'sometimes|string',
            'plate' => 'sometimes|string',
            'color' => 'sometimes|string',
            'estimated_price' => 'sometimes|nullable|numeric|min:0',
            'sale_price' => 'sometimes|nullable|numeric|min:0',
        ]);

        $car->update($validated);

        return response()->json($car->load(['brand', 'expenses', 'sale', 'images']));
    }

    public function destroy(Car $car)
    {
        $car->delete();
        return response()->json(null, 204);
    }
}

