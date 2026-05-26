<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'selling_price',
        'category',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function openingStocks(): HasMany
    {
        return $this->hasMany(OpeningStock::class);
    }

    public function todayOpeningStock(): ?int
    {
        $record = $this->openingStocks()
            ->whereDate('date', today())
            ->first();

        return $record?->quantity;
    }
}
