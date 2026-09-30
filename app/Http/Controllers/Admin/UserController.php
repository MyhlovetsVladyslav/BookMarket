<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(Request $request): Response
    {
        $query = User::withCount('books')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            if (in_array($role, ['super_admin', 'senior_admin', 'user'], true)) {
                $query->where('role', $role);
            }
        }

        $users = $query->paginate(15)->withQueryString();

        return Inertia::render('admin/Users', [
            'users' => $users,
            'filters' => [
                'search' => $request->input('search', ''),
                'role' => $request->input('role', ''),
            ],
            'can_manage_roles' => $request->user()->isSuperAdmin(),
            'current_user_id' => $request->user()->id,
        ]);
    }

    /**
     * Update a user's role (Super Admin only).
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        // Enforce super admin authorization
        if (! $request->user()->isSuperAdmin()) {
            abort(403, 'Лише головний адміністратор може призначати та змінювати ролі.');
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:user,senior_admin,super_admin'],
        ]);

        // Prevent demoting the last super_admin
        if ($user->role === 'super_admin' && $validated['role'] !== 'super_admin') {
            $superAdminCount = User::where('role', 'super_admin')->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Неможливо понизити останнього головного адміністратора системи.');
            }
        }

        $user->update([
            'role' => $validated['role'],
        ]);

        return back()->with('success', "Роль користувача {$user->name} успішно змінено на '{$validated['role']}'.");
    }
}
