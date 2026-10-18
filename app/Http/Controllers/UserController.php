<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::orderBy('name')->paginate(25)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()],
            'role' => ['required', Rule::in($this->roles())],
        ]);

        $user = User::create($data);

        ActivityLog::record('create', 'users', $user->id, 'Menambah pengguna '.$user->name);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', Password::min(8)->letters()->numbers()],
            'role' => ['required', Rule::in($this->roles())],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($user->isAdmin() && $data['role'] !== User::ROLE_ADMIN && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->with('error', 'Tidak bisa mengubah role admin terakhir.');
        }

        $user->update($data);

        ActivityLog::record('update', 'users', $user->id, 'Mengubah pengguna '.$user->name);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->isAdmin() && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return redirect()->route('users.index')->with('error', 'Tidak bisa menghapus admin terakhir.');
        }

        ActivityLog::record('delete', 'users', $user->id, 'Menghapus pengguna '.$user->name);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * @return array<int, string>
     */
    private function roles(): array
    {
        return [User::ROLE_ADMIN, User::ROLE_BENDAHARA, User::ROLE_VIEWER];
    }
}
