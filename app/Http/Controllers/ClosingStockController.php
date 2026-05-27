<?php

namespace App\Http\Controllers;

use App\Models\ClosingStock;
use App\Models\OpeningStock;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
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

        $products      = Product::orderBy('name')->get();
        $productIds    = $products->pluck('id');

        // Batch queries — avoid N+1
        $closingStocks  = ClosingStock::whereIn('product_id', $productIds)
            ->whereDate('date', $date)->get()->keyBy('product_id');
        $openingStocks  = OpeningStock::whereIn('product_id', $productIds)
            ->whereDate('date', $date)->get()->keyBy('product_id');
        $purchaseTotals = Purchase::whereIn('product_id', $productIds)
            ->whereDate('purchase_date', $date)
            ->selectRaw('product_id, SUM(quantity) as total')
            ->groupBy('product_id')->pluck('total', 'product_id');
        $saleTotals     = Sale::whereIn('product_id', $productIds)
            ->whereDate('sale_date', $date)
            ->selectRaw('product_id, SUM(quantity) as total')
            ->groupBy('product_id')->pluck('total', 'product_id');

        $products = $products->map(function ($product) use ($closingStocks, $openingStocks, $purchaseTotals, $saleTotals) {
            $stock        = $closingStocks->get($product->id);
            $opening      = $openingStocks->get($product->id);
            $purchasedQty = (int) ($purchaseTotals[$product->id] ?? 0);
            $soldQty      = (int) ($saleTotals[$product->id] ?? 0);

            $product->closing_for_date          = $stock;
            $product->closing_qty_for_date      = $stock?->quantity ?? null;
            $product->closing_notes_for_date    = $stock?->notes ?? null;
            $product->opening_qty_for_date      = $opening?->quantity;
            $product->purchased_qty_for_date    = $purchasedQty;
            $product->sold_qty_for_date         = $soldQty;
            // Expected closing = opening + purchases - sales (null if no opening stock recorded)
            $product->expected_closing_for_date = $opening !== null
                ? max(0, $opening->quantity + $purchasedQty - $soldQty)
                : null;
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
