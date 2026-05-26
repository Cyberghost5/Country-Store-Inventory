<?php

namespace App\Http\Controllers;

use App\Models\ClosingStock;
use App\Models\Product;
use Illuminate\Http\Request;

class ClosingStockController extends Controller
{
    /* ── Index ── */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $date = $request->input('date', today()->toDateString());

        $products = Product::orderBy('name')->get()->map(function ($product) use ($date) {
            $stock = ClosingStock::where('product_id', $product->id)
                ->whereDate('date', $date)
                ->first();
            $product->closing_for_date       = $stock;
            $product->closing_qty_for_date   = $stock?->quantity ?? null;
            $product->closing_notes_for_date = $stock?->notes ?? null;
            return $product;
        });

        $history = ClosingStock::with('product', 'recorder')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->take(100)
            ->get();

        $alreadySaved = ClosingStock::whereDate('date', $date)->exists();

        $stats = [
            'today_recorded' => ClosingStock::whereDate('date', today())->count(),
            'total_products' => Product::count(),
        ];

        return view('closing_stock.index', compact('user', 'products', 'history', 'date', 'stats', 'alreadySaved'));
    }

    /* ── Store / Update (batch upsert) ── */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $data = $request->validate([
            'date'              => 'required|date',
            'stocks'            => 'nullable|array',
            'stocks.*.quantity' => 'nullable|integer|min:0',
            'stocks.*.notes'    => 'nullable|string|max:500',
        ]);

        $date   = $data['date'];
        $stocks = $data['stocks'] ?? [];
        $saved  = 0;

        $validIds = Product::whereIn('id', array_keys($stocks))->pluck('id')->toArray();

        foreach ($validIds as $productId) {
            $entry    = $stocks[$productId] ?? [];
            $quantity = isset($entry['quantity']) && $entry['quantity'] !== null
                ? (int) $entry['quantity']
                : null;

            if ($quantity === null) {
                continue;
            }

            ClosingStock::updateOrCreate(
                ['product_id' => $productId, 'date' => $date],
                [
                    'quantity'    => $quantity,
                    'notes'       => $entry['notes'] ?? null,
                    'recorded_by' => $user->id,
                ]
            );
            $saved++;
        }

        $message = $saved > 0
            ? "Closing stock saved for {$saved} " . ($saved === 1 ? 'product' : 'products') . '.'
            : 'No entries saved — fill in at least one quantity.';

        return redirect()->route('closing_stock.index', ['date' => $date])
            ->with('status', $message);
    }

    /* ── Destroy ── */
    public function destroy(ClosingStock $closingStock)
    {
        $user = request()->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        $date = $closingStock->date->toDateString();
        $closingStock->delete();

        return redirect()->route('closing_stock.index', ['date' => $date])
            ->with('status', 'Closing stock entry deleted.');
    }
}
