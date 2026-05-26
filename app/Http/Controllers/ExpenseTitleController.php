<?php

namespace App\Http\Controllers;

use App\Models\ExpenseTitle;
use Illuminate\Http\Request;

class ExpenseTitleController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:expense_titles,name',
        ]);

        ExpenseTitle::create([
            'name'       => $data['name'],
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('expenses.index')
            ->with('titles_status', 'Expense type "' . $data['name'] . '" added.');
    }

    public function destroy(Request $request, ExpenseTitle $expenseTitle)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $name = $expenseTitle->name;
        $expenseTitle->delete();

        return redirect()->route('expenses.index')
            ->with('titles_status', 'Expense type "' . $name . '" removed.');
    }
}
