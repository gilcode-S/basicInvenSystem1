<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $stock = Stock::with(['item', 'location'])->get();
        return response()->json($stock);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //


    }

    /**
     * Display the specified resource.
     */
    public function show($itemId, $locationId)
    {
        //
        $stock = Stock::with(['item', 'location'])
            ->where('item_id', $itemId)
            ->where('location_id', $locationId)
            ->firstOrFail();

        return response()->json($stock);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $itemId ,$locationId)
    {
        //
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'max_quantity' => 'required|integer|min: 0'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        };

        $stock = Stock::where('item_id', $itemId)->where('location_id', $locationId)->firstOrFail();

        $stock->update($request->all());

        return response()->json($stock);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getLowStock()
    {
        $lowStock = Stock::with(['item', 'location'])
            ->where('quantity <= min_quantity')
            ->get();

        return response()->json($lowStock);
    }
}
