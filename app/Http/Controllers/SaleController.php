<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\SaleNotification;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /* ── Index ── */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $date = $request->input('date', today()->toDateString());

        // Admin sees all; staff sees only their own
        $query = Sale::with('product', 'seller')
            ->whereDate('sale_date', $date)
            ->orderByDesc('created_at');

        if ($user->role === 'staff') {
            $query->where('sold_by', $user->id);
        }

        $sales = $query->get();

        $products = Product::orderBy('name')->get();

        $stats = [
            'today_total'  => Sale::whereDate('sale_date', today())
                ->when($user->role === 'staff', fn($q) => $q->where('sold_by', $user->id))
                ->sum('total_amount'),
            'today_count'  => Sale::whereDate('sale_date', today())
                ->when($user->role === 'staff', fn($q) => $q->where('sold_by', $user->id))
                ->count(),
            'date_total'   => Sale::whereDate('sale_date', $date)
                ->when($user->role === 'staff', fn($q) => $q->where('sold_by', $user->id))
                ->sum('total_amount'),
        ];

        return view('sales.index', compact('user', 'sales', 'products', 'date', 'stats'));
    }

    /* ── Store ── */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $data = $request->validate([
            'product_id'      => 'required|exists:products,id',
            'quantity'        => 'required|integer|min:1',
            'sale_date'       => 'required|date',
            'notes'           => 'nullable|string|max:500',
            'cash_amount'     => 'nullable|numeric|min:0',
            'transfer_amount' => 'nullable|numeric|min:0',
        ]);

        $product        = Product::findOrFail($data['product_id']);
        $totalAmount    = $product->selling_price * $data['quantity'];
        $cashAmount     = (float) ($data['cash_amount'] ?? 0);
        $transferAmount = (float) ($data['transfer_amount'] ?? 0);

        if ($cashAmount <= 0 && $transferAmount <= 0) {
            return back()
                ->withErrors(['payment' => 'Please select at least one payment method.'])
                ->withInput();
        }

        if (abs(($cashAmount + $transferAmount) - $totalAmount) >= 0.01) {
            return back()
                ->withErrors(['payment' => 'Payment amounts (₦' . number_format($cashAmount + $transferAmount, 2) . ') must equal the sale total (₦' . number_format($totalAmount, 2) . ').'])
                ->withInput();
        }

        $sale = Sale::create([
            'product_id'      => $product->id,
            'quantity'        => $data['quantity'],
            'unit_price'      => $product->selling_price,
            'total_amount'    => $totalAmount,
            'cash_amount'     => $cashAmount,
            'transfer_amount' => $transferAmount,
            'sold_by'         => $user->id,
            'sale_date'       => $data['sale_date'],
            'notes'           => $data['notes'] ?? null,
        ]);

        $sale->load('product');
        $this->notifyAdmins($sale, $user);

        return redirect()->route('sales.index', ['date' => $data['sale_date']])
            ->with('status', 'Sale recorded successfully.');
    }

    /* ── Notify all admins except the recorder ── */
    private function notifyAdmins(Sale $sale, User $recorder): void
    {
        User::whereIn('role', ['admin', 'super_admin'])
            ->where('id', '!=', $recorder->id)
            ->get()
            ->each(fn($admin) => $admin->notify(new SaleNotification($sale, $recorder)));
    }

    /* ── Destroy ── */
    public function destroy(Request $request, Sale $sale)
    {
        $user = $request->user();

        // Staff can only delete their own; admin can delete any
        if ($user->role === 'staff' && $sale->sold_by !== $user->id) {
            abort(403);
        }

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $date = $sale->sale_date->toDateString();
        $sale->delete();

        return redirect()->route('sales.index', ['date' => $date])
            ->with('status', 'Sale entry deleted.');
    }
}
