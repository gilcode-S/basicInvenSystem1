<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $items = Item::with(['locations'])->get();
        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'sku' => 'required|string|unique:items',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'unit_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $item = Item::create($request->all());

        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
        $item->load(['location', 'transfers']);
        return response()->json($item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        //
        $validator = Validator::make($request->all(), [
            'sku' => 'sometimes|required|string|unique:items,sku',
            $item->id,
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|string',
            'unit_price' => 'sometimes|required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors', $validator->errors()], 422);
        }

        $item->update($request->all());

        return response()->json($item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        //
        $item->delete();
        return response()->json(['message' => 'Item deleted successfully']);
    }

    public function updateStock(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'location_Id' => 'required|exists:location,id',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'max_quantity' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $stock = Stock::updateOrCreate(
            [
                'item_id' => $item->id,
                'location_id' => $request->location_id
            ],
            [
                'quantity' => $request->quantity,
                'min_quantity' => $request->min_quantity ?? 10,
                'max_quantity' => $request->max_quantity
            ]
        );

        return response()->json($stock);
    }
}
