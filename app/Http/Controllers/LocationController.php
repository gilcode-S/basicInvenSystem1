<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $locations = Location::all();
        return response()->json($locations);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'type' => 'required|in:warehouse, store',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string'
        ]);

        if($validator->fails())
        {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $locations = Location::create($request->all());
        
        return response()->json($locations, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        //
        $location->load(['user', 'items']);
        return response()->json($location);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
            'type' => 'sometimes|required|in:warehouse, store',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ]);

        if($validator->fails())
        {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $location->update($request->all);

        return response()->json($location);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        //
        $location->delete();
        return response()->json(['message' => 'Location deleted successfully']);
    }
}
