<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'description', 'amount', 'category',
        'expense_date', 'recorded_by', 'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function categories(): array
    {
        return [
            'food'        => 'Food & Beverages',
            'transport'   => 'Transport',
            'utilities'   => 'Utilities',
            'supplies'    => 'Supplies',
            'maintenance' => 'Maintenance',
            'other'       => 'Other',
        ];
    }
}
