<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('name')->get();
        return response()->json(['data' => $brands]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $brand = Brand::create($validated);
        return response()->json(['data' => $brand], 201);
    }

    public function show(Brand $brand)
    {
        return response()->json($brand);
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $brand->update($validated);
        return response()->json(['data' => $brand]);
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return response()->json(null, 204);
    }
}

