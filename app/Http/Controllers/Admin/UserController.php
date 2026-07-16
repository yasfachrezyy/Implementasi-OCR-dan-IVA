<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function indexStaff()
    {
        $users = User::whereIn('role', ['notaris', 'staff'])
                     ->latest()->paginate(10);
        return view('admin.users.indexStaff', compact('users'));
    }

    public function indexKlien()
    {
        $users = User::where('role', 'klien')
                     ->latest()->paginate(10);
        return view('admin.users.indexKlien', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:notaris,staff,klien',
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        if ($request->role == 'klien') {
            return redirect()->route('notaris.klien.index')->with('success', 'Klien berhasil ditambahkan.');
        }
        return redirect()->route('notaris.staff.index')->with('success', 'Staff berhasil ditambahkan.');
    }
    
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:notaris,staff,klien',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (empty($validatedData['password'])) {
            unset($validatedData['password']);
        } else {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);
        
        if ($user->role == 'klien') {
            return redirect()->route('notaris.klien.index')->with('success', "User '{$user->name}' berhasil diperbarui.");
        }
        return redirect()->route('notaris.staff.index')->with('success', "User '{$user->name}' berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        $userName = $user->name;
        $user->delete();
        return redirect()->back()->with('success', "User '{$userName}' berhasil dihapus.");
    }
}