<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    //
    use HasFactory;
    
    protected $fillable = [
        'name',
        'address',
        'type',
        'contact_email',
        'contact_phone',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
    // one to many; locations has many users nga diba!
    public function users():HasMany
    {
        return $this->hasMany(User::class);
    }

    // many to many: location has many times stock diba1
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'stocks')->withPivot('quantity', 'min_quantity', 'max_quantity');
    }

    // transfer from 
    public function transfersFrom(): HasMany
    {
        return $this->hasMany(Transfer::class, 'from_location_id');
    }

    // transfer to 
     public function transferTo(): HasMany
    {
        return $this->hasMany(Transfer::class, 'to_location_id');
    }

}
