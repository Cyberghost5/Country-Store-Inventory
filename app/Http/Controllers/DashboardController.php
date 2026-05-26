<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\OpeningStock;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $stats = [
            'total_products'   => Product::count(),
            'today_stock'      => OpeningStock::whereDate('date', today())->count(),
            'today_sales'      => Sale::whereDate('sale_date', today())
                ->when($user->role === 'staff', fn($q) => $q->where('sold_by', $user->id))
                ->sum('total_amount'),
            'today_expenses'   => Expense::whereDate('expense_date', today())
                ->when($user->role === 'staff', fn($q) => $q->where('recorded_by', $user->id))
                ->sum('amount'),
        ];

        return view('dashboard', compact('user', 'stats'));
    }
}
