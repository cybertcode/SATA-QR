<?php

namespace App\Http\Controllers\Sata\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return view('sata.users.index');
    }

    public function profile()
    {
        $user = Auth::user()->load('tenant');
        return view('sata.users.profile', compact('user'));
    }

    public function updateProfile(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'dni' => ['nullable', 'string', 'digits:8', 'unique:users,dni,' . $user->id],
            'cargo' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->fill($validated);
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }
        $user->save();

        return back()->with('success', 'Su perfil ha sido actualizado.');
    }
}
