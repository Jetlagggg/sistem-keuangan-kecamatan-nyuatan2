@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Profil Saya</h1>
            <p class="page-sub">Perbarui nama, email, dan kata sandi akun yang dipakai untuk masuk ke buku kas.</p>
        </div>
        <div class="page-actions">
            <span class="tag {{ $user->role === 'admin' ? 'tag-solid' : 'tag-muted' }}">{{ $user->role }}</span>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <section class="sheet" aria-labelledby="profil-title">
                <div class="sheet-head">
                    <h2 class="sheet-title" id="profil-title">Data Akun</h2>
                    <span class="sheet-meta">{{ $user->email }}</span>
                </div>
                <div class="sheet-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label" for="profile-name">Nama</label>
                            <input type="text" id="profile-name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="profile-email">Email</label>
                            <input type="email" id="profile-email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <hr class="form-divider">
                        <p class="form-hint mb-3">Kosongkan kolom kata sandi jika tidak ingin mengubahnya.</p>

                        <div class="mb-3">
                            <label class="form-label" for="profile-current-password">Kata Sandi Saat Ini</label>
                            <input type="password" id="profile-current-password" name="current_password" class="form-control" autocomplete="current-password">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="profile-password">Kata Sandi Baru</label>
                                <input type="password" id="profile-password" name="password" class="form-control" autocomplete="new-password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="profile-password-confirmation">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" id="profile-password-confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
