@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Daftar Pengguna</h1>
            <p class="page-sub">Kelola akun dan hak akses: admin penuh, bendahara membuat dan mengubah, viewer hanya membaca.</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Tambah Pengguna
            </button>
        </div>
    </div>

    <section class="sheet" aria-labelledby="users-title">
        <div class="sheet-head">
            <h2 class="sheet-title" id="users-title">Akun Terdaftar</h2>
            <span class="sheet-meta">{{ $users->total() }} pengguna</span>
        </div>
        <div class="table-responsive">
            <table class="table-ledger">
                <thead>
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col" class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $roleClass = match ($user->role) {
                            \App\Models\User::ROLE_ADMIN => 'tag-solid',
                            \App\Models\User::ROLE_BENDAHARA => 'tag-green',
                            default => 'tag-muted',
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="cell-strong">{{ $user->name }}</span>
                            @if($user->is(auth()->user()))
                                <span class="tag tag-muted ms-1">Anda</span>
                            @endif
                        </td>
                        <td class="cell-date">{{ $user->email }}</td>
                        <td><span class="tag {{ $roleClass }}">{{ $user->role }}</span></td>
                        <td class="center" style="white-space: nowrap;">
                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                Edit
                            </button>
                            @if(! $user->is(auth()->user()))
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun ini? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="4">Belum ada pengguna.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div>
        {{ $users->links() }}
    </div>

    @foreach($users as $user)
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title" id="editUserLabel{{ $user->id }}">Edit Pengguna</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="user-name-{{ $user->id }}">Nama</label>
                            <input type="text" id="user-name-{{ $user->id }}" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="user-email-{{ $user->id }}">Email</label>
                            <input type="email" id="user-email-{{ $user->id }}" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="user-role-{{ $user->id }}">Role</label>
                            <select name="role" id="user-role-{{ $user->id }}" class="form-select" required>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="bendahara" {{ $user->role === 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                                <option value="viewer" {{ $user->role === 'viewer' ? 'selected' : '' }}>Viewer</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="user-password-{{ $user->id }}">Password Baru</label>
                            <input type="password" id="user-password-{{ $user->id }}" name="password" class="form-control" autocomplete="new-password" placeholder="Kosongkan bila tidak diubah">
                            <small class="form-hint">Minimal 8 karakter.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title" id="createUserLabel">Tambah Pengguna Baru</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="new-user-name">Nama</label>
                            <input type="text" id="new-user-name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="new-user-email">Email</label>
                            <input type="email" id="new-user-email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="new-user-role">Role</label>
                            <select name="role" id="new-user-role" class="form-select" required>
                                <option value="bendahara">Bendahara</option>
                                <option value="admin">Admin</option>
                                <option value="viewer">Viewer</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="new-user-password">Password</label>
                            <input type="password" id="new-user-password" name="password" class="form-control" autocomplete="new-password" required>
                            <small class="form-hint">Minimal 8 karakter.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
