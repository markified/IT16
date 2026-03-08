<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('is_active', true)->orderBy('created_at', 'DESC')->get();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Determine which roles the current user can assign
        $allowedRoles = ['inventory', 'security'];

        // Only superadmin can create admin and superadmin users
        if (auth()->user()->isAdmin() && auth()->user()->role === 'superadmin') {
            $allowedRoles = ['superadmin', 'admin', 'inventory', 'security'];
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
            'role' => ['required', Rule::in($allowedRoles)],
        ]);

        try {
            // Inventory and Security roles require approval
            $requiresApproval = in_array($request->role, ['inventory', 'security']);

            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => $validated['role'],
                'is_approved' => ! $requiresApproval,
            ]);

            $message = 'User added successfully.';
            if ($requiresApproval) {
                $message .= ' The user will need to be approved before they can log in.';
            }

            return redirect()->route('users.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Determine which roles the current user can assign
        $allowedRoles = ['inventory', 'security'];

        // Only superadmin can assign admin and superadmin roles
        if (auth()->user()->isAdmin() && auth()->user()->role === 'superadmin') {
            $allowedRoles = ['superadmin', 'admin', 'inventory', 'security'];
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in($allowedRoles)],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
        ]);

        // Only update password if provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        try {
            $user->update($validated);

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update user. Please try again.');
        }
    }

    /**
     * Approve a user account (for admin/superadmin registrations).
     */
    public function approve(string $id)
    {
        $user = User::findOrFail($id);

        // Only superadmin can approve admin/superadmin users
        if (! auth()->user()->isAdmin()) {
            return redirect()->route('users.index')->with('error', 'Unauthorized action.');
        }

        if ($user->is_approved) {
            return redirect()->route('users.index')->with('info', 'User is already approved.');
        }

        try {
            $user->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            return redirect()->route('users.index')->with('success', 'User approved successfully. They can now log in.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Failed to approve user. Please try again.');
        }
    }

    /**
     * Archive the specified user (soft delete by setting is_active to false).
     */
    public function destroy(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Prevent archiving own account
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot archive your own account.');
        }

        // Prevent archiving admin or superadmin users
        if (in_array($user->role, ['admin', 'superadmin'])) {
            return redirect()->route('users.index')->with('error', 'Admin and Superadmin users cannot be archived.');
        }

        // Require password confirmation for archiving
        if (! $request->confirm_password) {
            return redirect()->route('users.index')->with('error', 'Password confirmation is required to archive users.');
        }

        // Verify password
        if (! Hash::check($request->confirm_password, auth()->user()->password)) {
            return redirect()->route('users.index')->with('error', 'Incorrect password. Please try again.');
        }

        try {
            $user->update(['is_active' => false]);

            return redirect()->route('users.index')->with('success', 'User archived successfully.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Failed to archive user. Please try again.');
        }
    }

    /**
     * Display archived users.
     */
    public function archived()
    {
        $users = User::where('is_active', false)->orderBy('updated_at', 'DESC')->get();

        return view('users.archived', compact('users'));
    }

    /**
     * Restore an archived user.
     */
    public function restore(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['is_active' => true]);

            return redirect()->back()->with('success', 'User restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore user. Please try again.');
        }
    }

    /**
     * Permanently delete an archived user (superadmin only).
     */
    public function permanentDelete(string $id)
    {
        // Only superadmin can permanently delete users
        if (auth()->user()->role !== 'superadmin') {
            return redirect()->back()->with('error', 'Unauthorized. Only superadmin can permanently delete users.');
        }

        try {
            $user = User::findOrFail($id);

            // Only allow permanent deletion of archived users
            if ($user->is_active) {
                return redirect()->back()->with('error', 'Only archived users can be permanently deleted.');
            }

            // Prevent deleting yourself
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }

            $userName = $user->name;
            $user->delete();

            return redirect()->back()->with('success', "User '{$userName}' has been permanently deleted.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete user permanently. Please try again.');
        }
    }
}
