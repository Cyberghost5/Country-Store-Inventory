<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'product_id', 'quantity', 'unit_cost', 'total_cost',
        'supplier_id', 'purchase_date', 'recorded_by', 'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'unit_cost'     => 'decimal:2',
        'total_cost'    => 'decimal:2',
        'quantity'      => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
