<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterUserController extends Controller
{
    public function index()
    {
        return view('admin.master.users.index', [
            'users' => User::where('is_admin', true)->orderBy('name')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.master.users.form', ['user' => new User]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $data['is_admin'] = true;

        User::create($data);

        return redirect()->route('admin.master.users.index')->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        abort_unless($user->is_admin, 404);

        return view('admin.master.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->is_admin, 404);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);

        return redirect()->route('admin.master.users.index')->with('success', 'Akun admin berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->is_admin, 404);

        if ($user->is(auth()->user())) {
            return back()->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }

        if (User::where('is_admin', true)->count() <= 1) {
            return back()->with('error', 'Admin terakhir tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->route('admin.master.users.index')->with('success', 'Akun admin berhasil dihapus.');
    }
}
