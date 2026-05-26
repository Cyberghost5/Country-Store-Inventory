<?php

namespace App\Http\Controllers;

use App\Models\ClosingStock;
use App\Models\Expense;
use App\Models\OpeningStock;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        $type = $request->input('type', 'daily');
        $date = $request->input('date', today()->toDateString());

        if ($type === 'weekly') {
            return $this->weeklyReport($user, $date);
        }

        return $this->dailyReport($user, $date);
    }

    /* ── Daily ── */
    private function dailyReport($user, string $date)
    {
        $type = 'daily';

        $sales    = Sale::with('product', 'seller')->whereDate('sale_date', $date)->orderByDesc('created_at')->get();
        $expenses = Expense::with('recorder')->whereDate('expense_date', $date)->orderByDesc('created_at')->get();
        $purchases = Purchase::with('product', 'supplier', 'recorder')->whereDate('purchase_date', $date)->orderByDesc('created_at')->get();

        $totalSales     = $sales->sum('total_amount');
        $totalExpenses  = $expenses->sum('amount');
        $totalPurchases = $purchases->sum('total_cost');

        $dailyStats = [
            'total_sales'      => $totalSales,
            'total_expenses'   => $totalExpenses,
            'total_purchases'  => $totalPurchases,
            'net'              => $totalSales - $totalExpenses - $totalPurchases,
            'sales_count'      => $sales->count(),
            'expenses_count'   => $expenses->count(),
            'purchases_count'  => $purchases->count(),
        ];

        // Top 5 products by revenue
        $topProducts = $sales->groupBy('product_id')
            ->map(fn($g) => [
                'name'  => $g->first()->product?->name ?? 'Unknown',
                'qty'   => $g->sum('quantity'),
                'total' => (float) $g->sum('total_amount'),
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values();

        // Expense breakdown by category
        $expenseByCategory = $expenses->groupBy('category')
            ->map(fn($g) => round((float) $g->sum('amount'), 2))
            ->sortByDesc(fn($v) => $v);

        // Purchases by supplier
        $purchaseBySupplier = $purchases->groupBy('supplier_id')
            ->map(fn($g) => [
                'name'  => $g->first()->supplier?->name ?? 'Unknown',
                'total' => round((float) $g->sum('total_cost'), 2),
            ])
            ->sortByDesc('total')
            ->values();

        // Absolute Sales = closing inventory yesterday + purchases today - closing inventory today
        $carbonDate       = Carbon::parse($date);
        $openingStocks    = OpeningStock::with('product')
            ->whereDate('date', $date)
            ->orderBy('product_id')
            ->get();
        $closingYesterday = $this->inventoryValue($carbonDate->copy()->subDay()->toDateString());
        $closingToday     = $this->inventoryValue($carbonDate->toDateString());
        $absStats = [
            'closing_start'  => $closingYesterday,
            'purchases'      => $totalPurchases,
            'closing_end'    => $closingToday,
            'absolute_sales' => $closingYesterday + $totalPurchases - $closingToday,
            'has_data'       => $closingYesterday > 0 || $closingToday > 0,
            'start_label'    => 'Yesterday',
            'end_label'      => 'Today',
            'end_pending'    => $closingToday == 0 && $date === today()->toDateString(),
        ];

        return view('reports.index', compact(
            'user', 'type', 'date',
            'dailyStats', 'topProducts', 'expenseByCategory',
            'sales', 'expenses', 'purchases', 'purchaseBySupplier',
            'openingStocks', 'absStats'
        ));
    }

    /* ── Weekly ── */
    private function weeklyReport($user, string $date)
    {
        $type      = 'weekly';
        $weekStart = Carbon::parse($date)->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $weekDays = collect();
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $weekDays->push([
                'date'      => $day->toDateString(),
                'label'     => $day->format('D d/m'),
                'sales'     => (float) Sale::whereDate('sale_date', $day)->sum('total_amount'),
                'expenses'  => (float) Expense::whereDate('expense_date', $day)->sum('amount'),
                'purchases' => (float) Purchase::whereDate('purchase_date', $day)->sum('total_cost'),
            ]);
        }

        $weeklyStats = [
            'total_sales'     => $weekDays->sum('sales'),
            'total_expenses'  => $weekDays->sum('expenses'),
            'total_purchases' => $weekDays->sum('purchases'),
            'net'             => $weekDays->sum('sales') - $weekDays->sum('expenses') - $weekDays->sum('purchases'),
        ];

        // Top products for the whole week
        $weeklySales = Sale::with('product')
            ->whereBetween('sale_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get();

        $topProducts = $weeklySales->groupBy('product_id')
            ->map(fn($g) => [
                'name'  => $g->first()->product?->name ?? 'Unknown',
                'qty'   => $g->sum('quantity'),
                'total' => (float) $g->sum('total_amount'),
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values();

        // Expense by category for the week
        $weeklyExpenses = Expense::whereBetween('expense_date', [$weekStart->toDateString(), $weekEnd->toDateString()])->get();
        $expenseByCategory = $weeklyExpenses->groupBy('category')
            ->map(fn($g) => round((float) $g->sum('amount'), 2))
            ->sortByDesc(fn($v) => $v);

        // Absolute Sales for the week
        $closingWeekStart = $this->inventoryValue($weekStart->copy()->subDay()->toDateString());
        $closingWeekEnd   = $this->inventoryValue($weekEnd->toDateString());
        $weekPurchases    = $weekDays->sum('purchases');
        $absStats = [
            'closing_start'  => $closingWeekStart,
            'purchases'      => $weekPurchases,
            'closing_end'    => $closingWeekEnd,
            'absolute_sales' => $closingWeekStart + $weekPurchases - $closingWeekEnd,
            'has_data'       => $closingWeekStart > 0 || $closingWeekEnd > 0,
            'start_label'    => 'Week Start',
            'end_label'      => 'Week End',
            'end_pending'    => $closingWeekEnd == 0 && $weekEnd->gte(today()),
        ];

        return view('reports.index', compact(
            'user', 'type', 'date',
            'weekDays', 'weeklyStats', 'weekStart', 'weekEnd',
            'topProducts', 'expenseByCategory',
            'absStats'
        ));
    }

    /* ── Helper: total selling-price value of closing stock for a date ── */
    private function inventoryValue(string $date): float
    {
        return (float) ClosingStock::with('product')
            ->whereDate('date', $date)
            ->get()
            ->sum(fn($s) => $s->quantity * ($s->product?->selling_price ?? 0));
    }
}

