<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PeopleController extends Controller
{
    /* ── Index ── */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        $people = User::orderByRaw("FIELD(role,'super_admin','admin','staff')")
            ->orderBy('name')
            ->get();

        $stats = [
            'total'  => $people->count(),
            'admins' => $people->whereIn('role', ['admin', 'super_admin'])->count(),
            'staff'  => $people->where('role', 'staff')->count(),
        ];

        return view('people.index', compact('user', 'people', 'stats'));
    }

    /* ── Store ── */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        // super_admin can create admin or staff; admin can only create staff
        $allowedRoles = $user->isSuperAdmin() ? ['admin', 'staff'] : ['staff'];

        $data = $request->validate([
            'name'                  => 'required|string|max:100',
            'phone'                 => 'required|string|max:20|unique:users,phone',
            'role'                  => 'required|in:' . implode(',', $allowedRoles),
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        User::create([
            'name'     => $data['name'],
            'phone'    => $data['phone'],
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('people.index')
            ->with('status', 'User created successfully.');
    }

    /* ── Destroy ── */
    public function destroy(Request $request, User $person)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        if ($person->id === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // super_admin accounts are protected entirely
        if ($person->role === 'super_admin') {
            abort(403, 'Super admin accounts cannot be deleted.');
        }

        // Regular admin cannot delete other admin accounts
        if (!$user->isSuperAdmin() && $person->role === 'admin') {
            abort(403, 'You do not have permission to delete admin accounts.');
        }

        $person->delete();

        return redirect()->route('people.index')
            ->with('status', "User \"{$person->name}\" removed.");
    }
}
