@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Daftar Pengguna</h5>
            <p class="text-muted small mb-0">Kelola akun dan hak akses (admin, bendahara, viewer).</p>
        </div>
        <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fa-solid fa-user-plus me-2"></i> Tambah Pengguna
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small">
                        <tr>
                            <th class="ps-4">Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $user->name }}</div>
                                @if($user->is(auth()->user()))
                                    <span class="badge bg-light text-dark border">Anda</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleClass = match ($user->role) {
                                        \App\Models\User::ROLE_ADMIN => 'danger',
                                        \App\Models\User::ROLE_BENDAHARA => 'primary',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $roleClass }} text-uppercase">{{ $user->role }}</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning fw-bold"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUserModal{{ $user->id }}">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                @if(! $user->is(auth()->user()))
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengguna ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger fw-bold">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada pengguna.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>

    @foreach($users as $user)
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Pengguna</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="bendahara" {{ $user->role === 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                                <option value="viewer" {{ $user->role === 'viewer' ? 'selected' : '' }}>Viewer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password Baru</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password" placeholder="Kosongkan bila tidak diubah">
                            <small class="text-muted">Minimal 8 karakter.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning fw-bold">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Tambah Pengguna Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="bendahara">Bendahara</option>
                                <option value="admin">Admin</option>
                                <option value="viewer">Viewer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password" required>
                            <small class="text-muted">Minimal 8 karakter.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary fw-bold">Simpan Pengguna</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
