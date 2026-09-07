<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profil.password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.current_password' => 'Kata sandi lama yang Anda masukkan salah.',
        ]);

        $user = Auth::user();
        $user->password = $request->password;
        $user->save();

        ActivityLog::record('auth.password_change', 'Ganti kata sandi sendiri');

        return back()->with('success', 'Kata sandi berhasil diganti.');
    }
}