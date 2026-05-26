<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'product_id', 'quantity', 'unit_price', 'total_amount',
        'cash_amount', 'transfer_amount',
        'sold_by', 'sale_date', 'notes',
    ];

    protected $casts = [
        'sale_date'       => 'date',
        'unit_price'      => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'cash_amount'     => 'decimal:2',
        'transfer_amount' => 'decimal:2',
        'quantity'        => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'sold_by');
    }
}
