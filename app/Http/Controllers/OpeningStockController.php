<?php

namespace App\Http\Controllers;

use App\Models\ClosingStock;
use App\Models\OpeningStock;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OpeningStockController extends Controller
{
    /* ── Index ── */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        // Date filter — default to today
        $date = $request->input('date', today()->toDateString());

        // All products with their opening stock for the selected date
        $products      = Product::orderBy('name')->get();
        $productIds    = $products->pluck('id');
        $prevDate      = Carbon::parse($date)->subDay()->toDateString();

        // Batch queries — avoid N+1
        $openingStocks    = OpeningStock::whereIn('product_id', $productIds)
            ->whereDate('date', $date)->get()->keyBy('product_id');
        $prevClosingStocks = ClosingStock::whereIn('product_id', $productIds)
            ->whereDate('date', $prevDate)->get()->keyBy('product_id');

        $products = $products->map(function ($product) use ($openingStocks, $prevClosingStocks) {
            $stock       = $openingStocks->get($product->id);
            $prevClosing = $prevClosingStocks->get($product->id);

            $product->stock_for_date   = $stock;
            $product->qty_for_date     = $stock?->quantity ?? null;
            $product->notes_for_date   = $stock?->notes ?? null;
            // Suggested value from yesterday's closing stock
            $product->prev_closing_qty = $prevClosing?->quantity;
            return $product;
        });

        // History: last 30 days per product
        $history = OpeningStock::with('product', 'recorder')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->take(100)
            ->get();

        $alreadySaved = OpeningStock::whereDate('date', $date)->exists();

        $stats = [
            'today_recorded' => OpeningStock::whereDate('date', today())->count(),
            'total_products' => Product::count(),
        ];

        return view('opening_stock.index', compact('user', 'products', 'history', 'date', 'stats', 'alreadySaved'));
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

        // Only process IDs that actually exist in the products table
        $validIds = Product::whereIn('id', array_keys($stocks))->pluck('id')->toArray();

        foreach ($validIds as $productId) {
            $entry    = $stocks[$productId] ?? [];
            $quantity = isset($entry['quantity']) && $entry['quantity'] !== null
                ? (int) $entry['quantity']
                : null;

            if ($quantity === null) {
                continue; // skip cards left blank
            }

            OpeningStock::updateOrCreate(
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
            ? "Opening stock saved for {$saved} " . ($saved === 1 ? 'product' : 'products') . '.'
            : 'No entries saved — fill in at least one quantity.';

        return redirect()->route('opening_stock.index', ['date' => $date])
            ->with('status', $message);
    }

    /* ── Destroy ── */
    public function destroy(OpeningStock $openingStock)
    {
        $user = request()->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        $date = $openingStock->date->toDateString();
        $openingStock->delete();

        return redirect()->route('opening_stock.index', ['date' => $date])
            ->with('status', 'Opening stock entry deleted.');
    }
}
