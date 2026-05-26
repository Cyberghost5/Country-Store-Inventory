<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /* ── Index ── */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $query = Product::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->orderBy('name')->get();

        $stats = [
            'total_products' => Product::count(),
        ];

        return view('products.index', compact('user', 'products', 'stats'));
    }

    /* ── Store ── */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'unit'          => 'required|in:piece,carton,pack,kg,litre,dozen',
            'selling_price' => 'required|numeric|min:0',
            'category'      => 'nullable|string|max:100',
            'notes'         => 'nullable|string|max:500',
        ]);

        $data['created_by'] = $user->id;

        Product::create($data);

        return redirect()->route('products.index')
            ->with('status', 'Product "' . $data['name'] . '" added successfully.');
    }

    /* ── Update ── */
    public function update(Request $request, Product $product)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'unit'          => 'required|in:piece,carton,pack,kg,litre,dozen',
            'selling_price' => 'required|numeric|min:0',
            'category'      => 'nullable|string|max:100',
            'notes'         => 'nullable|string|max:500',
        ]);

        $product->update($data);

        return redirect()->route('products.index')
            ->with('status', 'Product "' . $product->name . '" updated successfully.');
    }

    /* ── Destroy ── */
    public function destroy(Product $product)
    {
        $user = request()->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        $name = $product->name;
        $product->delete();

        return redirect()->route('products.index')
            ->with('status', '"' . $name . '" has been deleted.');
    }
}
