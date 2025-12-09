<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,manager,operator,customer',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:8|confirmed',
            'notes' => 'nullable|string|max:500',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
            'password' => Hash::make($request->password),
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function show(User $user)
    {
        $photoSessions = $user->photoSessions()
            ->with(['photobox', 'frame'])
            ->latest()
            ->paginate(10);

        return view('admin.users.show', compact('user', 'photoSessions'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,manager,operator,customer',
            'status' => 'required|in:active,inactive,banned',
            'password' => 'nullable|string|min:8|confirmed',
            'notes' => 'nullable|string|max:500',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
            'notes' => $request->notes,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Prevent deleting if user has photo sessions
        if ($user->photoSessions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'User tidak dapat dihapus karena memiliki riwayat sesi foto.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function ban(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat memblokir akun sendiri.');
        }

        $user->update(['status' => 'banned']);

        return redirect()->back()
            ->with('success', 'User berhasil diblokir.');
    }

    public function unban(User $user)
    {
        $user->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'User berhasil diaktifkan kembali.');
    }
}
