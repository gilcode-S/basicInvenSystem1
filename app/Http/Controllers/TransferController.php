<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

use function Laravel\Prompts\error;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $transfer = Transfer::with(['item', 'fromLocation', 'toLocation', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($transfer);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists: items,id',
            'from_location_id' => 'required|exists: locations,id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'required|string'
        ]);

        if($validator->fails())
        {
            return response()->json(['error' => $validator->error()], 422);
        }

        // check if source location has enough stock
        $sourceStock = Stock::where('item_id', $request->item_id)
                ->where('location_id', $request->from_location_id)
                ->first();

        if(!$sourceStock || $sourceStock->quantity < $request->quantity)
        {
            return response()->json([
                'error ' => "insufficient stock at source location"
            ]);
        }

        DB::beginTransaction();

        try {
            //create transfer record
            $transfer = Transfer::create([
                'item_id' => $request->item_id,
                'from_location_id' => $request->from_location_id,
                'to_location_id' => $request->to_location_id,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
                'status' => 'pending',
            ]);

            // update source stock
            $sourceStock->decrement('quantity', $request->quantity);
            DB::commit();

            $transfer->load(['item', 'fromLocation', 'toLocation', 'user']);

            return response()->json($transfer, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'transfer failed!' . $e->getMessage()], 500);
        }
    }

    public function complete(Transfer $transfer)
    {
        if($transfer->status !== 'pending')
        {
            return response()->json(['error' => 'Transfer already completed or cancelled'], 422);
        }

        DB::beginTransaction();

        try{
            $destinationStock = Stock::firstOrCreate(
                [
                    'item_id' => $transfer->item_id,
                    'location_id' => $transfer->to_location_id
                ],
                [
                    'quantity' => 0
                ]
            );
            
            $destinationStock->increment('quantity', $transfer->quantity);

            //update transfer status
            $transfer->update([
                'status' => 'completed',
                'complete_at' => now(), 
            ]);

            DB::commit();

            $transfer->load(['item', 'fromLocation', 'toLocation', 'user']);
            return response()->json($transfer);
        } catch (\Exception $e ) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to complete transfer'. $e->getMessage()], 500);
        }
    }


    public function cancel(Transfer $transfer)
    {
        if($transfer->status !== 'pending')
        {
            return response()->json(['error' => 'Tranfer already completed or cancelled'], 422);
        }

        DB::beginTransaction();

        try {
            //code...
            $sourceStock = Stock::where('item_id', $transfer->item_id)
                    ->where('location_id', $transfer->from_location_id)
                    ->firstOrFail();

            $sourceStock->increment('quantity' , $transfer->quantity);

            // update transfer status
            $transfer->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json(['message' => 'Transfer cancelled succesfully']);
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            return response()->json(['error' => 'Failed to cancel tranfer' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
