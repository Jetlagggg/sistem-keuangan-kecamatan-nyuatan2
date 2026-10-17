<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - KECAMATAN NYUATAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">
</head>
<body class="bg-light" style="font-family: 'Inter', sans-serif;">

    <nav class="navbar navbar-dark mb-4 pt-3 pb-3" style="background: linear-gradient(to right, #1e3a8a, #1e40af);">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="navbar-brand fw-bold"><i class="fa-solid fa-users-gear me-2 text-warning"></i>Manajemen Pengguna</span>
            <div>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fa-solid fa-list me-1"></i> Transaksi
                </a>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fa-solid fa-tags me-1"></i> Anggaran
                </a>
                <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Riwayat
                </a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        @if(session('success')) <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger border-0 shadow-sm mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}</div> @endif
        @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
        @endif

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
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>
