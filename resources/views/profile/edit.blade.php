@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header-camat d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-circle-user me-2"></i> Profil Saya</span>
                    <span class="badge bg-primary text-uppercase">{{ $user->role }}</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="fw-bold small text-muted mb-1">NAMA</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold small text-muted mb-1">EMAIL</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <hr>
                        <p class="small text-muted mb-3">Kosongkan kolom kata sandi jika tidak ingin mengubahnya.</p>

                        <div class="mb-3">
                            <label class="fw-bold small text-muted mb-1">KATA SANDI SAAT INI</label>
                            <input type="password" name="current_password" class="form-control" autocomplete="current-password">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small text-muted mb-1">KATA SANDI BARU</label>
                                <input type="password" name="password" class="form-control" autocomplete="new-password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small text-muted mb-1">KONFIRMASI KATA SANDI BARU</label>
                                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-simpan w-100 py-2 mt-2">SIMPAN PERUBAHAN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
