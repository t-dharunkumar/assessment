<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', compact('users'));
    }

    public function toggleAccess(User $user)
    {
        if ($user->role === 'admin') {
            return back();
        }

        $user->update([
            'can_take_test' => ! $user->can_take_test
        ]);

        return back();
    }

    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            return back()->withErrors(['email' => 'User with this email already exists.']);
        }

        User::create([
            'name' => $request->email,
            'email' => $request->email,
            'role' => 'user',
            'is_invited' => true,
            'can_take_test' => true,
            'password' => bcrypt(Str::random(24))
        ]);

        return back()->with('success', 'User invited successfully.');
    }
}
