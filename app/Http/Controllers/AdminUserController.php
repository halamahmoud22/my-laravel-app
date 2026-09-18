<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminUserController extends Controller{
    public function __construct()
    {
        $this->middleware('can:manage-admins');
    }

    /**
     * Display a listing of admin users.
     */
    public function index()
    {
        $admins = User::whereIn('role', ['admin', 'super_admin'])->latest()->paginate(20);
        return view('admin.users.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created admin user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
        ]);

        $admin = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);
        $admin->role = $validated['role']; // explicit assignment bypasses $fillable on purpose
        $admin->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user created successfully.');
    }

    /**
     * Display the specified admin user.
     */
    public function show(User $user)
    {
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'User is not an admin.');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit(User $user)
    {
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'User is not an admin.');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified admin user in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'User is not an admin.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,super_admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role']; // explicit, same reason as store()
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user updated successfully.');
    }

    /**
     * Remove the specified admin user from storage.
     */
    public function destroy(User $user)
    {
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'User is not an admin.');
        }

        if ($user->role === 'super_admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Super admins cannot be deleted.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user deleted successfully.');
    }
}
