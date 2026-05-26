<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\PurchaseNotification;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /* ── Index ── */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $date = $request->input('date', today()->toDateString());

        $query = Purchase::with('product', 'supplier', 'recorder')
            ->whereDate('purchase_date', $date)
            ->orderByDesc('created_at');

        if ($user->role === 'staff') {
            $query->where('recorded_by', $user->id);
        }

        $purchases = $query->get();

        $stats = [
            'today_total' => Purchase::whereDate('purchase_date', today())
                ->when($user->role === 'staff', fn($q) => $q->where('recorded_by', $user->id))
                ->sum('total_cost'),
            'today_count' => Purchase::whereDate('purchase_date', today())
                ->when($user->role === 'staff', fn($q) => $q->where('recorded_by', $user->id))
                ->count(),
            'date_total'  => Purchase::whereDate('purchase_date', $date)
                ->when($user->role === 'staff', fn($q) => $q->where('recorded_by', $user->id))
                ->sum('total_cost'),
        ];

        $products  = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.index', compact(
            'user', 'purchases', 'date', 'stats', 'products', 'suppliers'
        ));
    }

    /* ── Store ── */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $data = $request->validate([
            'product_id'    => 'required|exists:products,id',
            'quantity'      => 'required|integer|min:1',
            'unit_cost'     => 'required|numeric|min:0.01',
            'supplier_id'   => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'notes'         => 'nullable|string|max:500',
        ]);

        $totalCost = $data['unit_cost'] * $data['quantity'];

        $purchase = Purchase::create([
            'product_id'    => $data['product_id'],
            'quantity'      => $data['quantity'],
            'unit_cost'     => $data['unit_cost'],
            'total_cost'    => $totalCost,
            'supplier_id'   => $data['supplier_id'],
            'purchase_date' => $data['purchase_date'],
            'recorded_by'   => $user->id,
            'notes'         => $data['notes'] ?? null,
        ]);

        $purchase->load('product', 'supplier');

        User::whereIn('role', ['admin', 'super_admin'])
            ->where('id', '!=', $user->id)
            ->get()
            ->each(fn($admin) => $admin->notify(new PurchaseNotification($purchase, $user)));

        return redirect()->route('purchases.index', ['date' => $data['purchase_date']])
            ->with('status', 'Purchase recorded successfully.');
    }

    /* ── Destroy ── */
    public function destroy(Request $request, Purchase $purchase)
    {
        $user = $request->user();

        if ($user->role === 'staff' && $purchase->recorded_by !== $user->id) {
            abort(403);
        }

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $purchase->delete();

        return back()->with('status', 'Purchase deleted.');
    }
}
