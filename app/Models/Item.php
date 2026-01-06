<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'category',
        'unit_price'
    ];

    protected $casts = [
        'unit_price' => 'decimal: 2'
    ];

    // relations 
    //many to many: Item belongs to many locations dahil sa stocks
    public function location(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'stocks')
                ->withPivot('quantity', 'min_quantity', 'max_quantity')
                ->withTimestamps();
    }

    //get stock for a specific location 
    public function stockAtLocation($locationId)
    {
        return $this->location()->where('locations_id', $locationId)->first()?->pivot->quantity ?? 0;
    }

    //transfer for this item
    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }
}
