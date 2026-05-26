<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name',
        ]);

        Supplier::create([
            'name'       => $data['name'],
            'created_by' => $request->user()->id,
        ]);

        return back()->with('suppliers_status', 'Supplier added.');
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $supplier->delete();

        return back()->with('suppliers_status', 'Supplier removed.');
    }
}
