<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OperatorController extends Controller
{
    public function index()
    {
        $operators = User::where('role', 'operator')->orderBy('name')->paginate(10);

        return view('operator.index', compact('operators'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $operator = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => $data['password'], // auto-hashed via 'hashed' cast
            'role' => 'operator',
            'is_active' => true,
        ]);

        ActivityLog::record('operator.create', "Akun operator baru: {$operator->username}");

        return back()->with('success', 'Akun operator ditambahkan.');
    }

    public function update(Request $request, User $operator)
    {
        // Cegah privilege escalation: pastikan target memang akun operator,
        // bukan admin/nasabah lain yang ID-nya ditebak/dimanipulasi via URL.
        abort_unless($operator->role === 'operator', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($operator->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
        ]);

        $operator->name = $data['name'];
        $operator->username = $data['username'];
        $operator->is_active = $data['is_active'];
        if (! empty($data['password'])) {
            $operator->password = $data['password'];
        }
        $operator->save();

        ActivityLog::record('operator.update', "Update akun operator: {$operator->username}");

        return back()->with('success', 'Akun operator diperbarui.');
    }
}
