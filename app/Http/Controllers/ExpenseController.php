<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseTitle;
use App\Models\User;
use App\Notifications\ExpenseNotification;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /* ── Index ── */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $date = $request->input('date', today()->toDateString());

        $query = Expense::with('recorder')
            ->whereDate('expense_date', $date)
            ->orderByDesc('created_at');

        if ($user->role === 'staff') {
            $query->where('recorded_by', $user->id);
        }

        $expenses = $query->get();

        $stats = [
            'today_total' => Expense::whereDate('expense_date', today())
                ->when($user->role === 'staff', fn($q) => $q->where('recorded_by', $user->id))
                ->sum('amount'),
            'today_count' => Expense::whereDate('expense_date', today())
                ->when($user->role === 'staff', fn($q) => $q->where('recorded_by', $user->id))
                ->count(),
            'date_total'  => Expense::whereDate('expense_date', $date)
                ->when($user->role === 'staff', fn($q) => $q->where('recorded_by', $user->id))
                ->sum('amount'),
        ];

        $categories = Expense::categories();

        $expenseTitles = ExpenseTitle::orderBy('name')->get();

        return view('expenses.index', compact('user', 'expenses', 'date', 'stats', 'categories', 'expenseTitles'));
    }

    /* ── Store ── */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $data = $request->validate([
            'description'  => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'category'     => 'required|string|in:food,transport,utilities,supplies,maintenance,other',
            'expense_date' => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        // Ensure the submitted description is a known expense title
        if (!ExpenseTitle::where('name', $data['description'])->exists()) {
            return back()
                ->withErrors(['description' => 'Please select a valid expense type from the list.'])
                ->withInput();
        }

        $expense = Expense::create([
            'description'  => $data['description'],
            'amount'       => $data['amount'],
            'category'     => $data['category'],
            'expense_date' => $data['expense_date'],
            'recorded_by'  => $user->id,
            'notes'        => $data['notes'] ?? null,
        ]);

        User::whereIn('role', ['admin', 'super_admin'])
            ->where('id', '!=', $user->id)
            ->get()
            ->each(fn($admin) => $admin->notify(new ExpenseNotification($expense, $user)));

        return redirect()->route('expenses.index', ['date' => $data['expense_date']])
            ->with('status', 'Expense recorded successfully.');
    }

    /* ── Destroy ── */
    public function destroy(Request $request, Expense $expense)
    {
        $user = $request->user();

        if ($user->role === 'staff' && $expense->recorded_by !== $user->id) {
            abort(403);
        }

        if (!$user->isAdminOrStaff()) {
            abort(403);
        }

        $date = $expense->expense_date->toDateString();
        $expense->delete();

        return redirect()->route('expenses.index', ['date' => $date])
            ->with('status', 'Expense deleted.');
    }
}
