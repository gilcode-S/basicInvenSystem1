<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Location;
use App\Models\Stock;
use App\Models\Transfer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function getDashboardData()
    {
        $data = [];

        // total locations count
        $data['total_locations'] = Location::count();
        $data['active_locations'] = Location::where('is_active', true)->count();

        //total items count
        $data['total_items'] = Item::count();

        //total stock value
        $data['low_stock_items'] = Stock::with(['item', 'location'])
            ->whereRaw('quantity <= min_quantity')
            ->count();

        //recent transfer
        $data['recent_transfers'] = Transfer::with(['item', 'fromLocation', 'toLocation'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Stock by location
        $data['stock_by_location'] = Location::withCount(['items as total_items'])
            ->with(['items' => function ($query) {
                $query->select('items.id', 'items.name', 'items.unit_price')
                    ->withPivot('quantity');
            }])
            ->get()
            ->map(function ($location) {
                $location->total_stock_value = $location->items->sum(function ($item) {
                    return $item->pivot->quantity * $item->unit_price;
                });
                return $location;
            });
        return response()->json($data);
    }
}
