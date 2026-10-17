# Sistem Keuangan Kecamatan Nyuatan

Aplikasi web monitoring & evaluasi (Monev) anggaran serta pencatatan transaksi keuangan Kantor Kecamatan Nyuatan, Kabupaten Kutai Barat. Dibangun dengan Laravel 12 + Blade + Bootstrap.

## Fitur

- **Autentikasi** dengan pembatasan percobaan login (anti brute-force).
- **Role pengguna**: `admin` (akses penuh), `bendahara` (input/ubah data), `viewer` (hanya lihat).
- **Monitoring & Evaluasi Anggaran** — pagu kantor, serapan, sisa alokasi, realisasi per pos.
- **Pencairan Dana (UP/GU)** — input dan hapus tahapan dana.
- **Transaksi Harian** — input, ubah, hapus (soft delete) dengan validasi pagu di sisi server.
- **Cetak Laporan PDF** (dompdf) dengan filter periode, kata kunci, dan sumber dana.
- **Riwayat Aktivitas** (audit trail) — mencatat login, logout, dan perubahan data beserta IP.
- **Manajemen Pengguna** (admin) — tambah/ubah/hapus akun, atur role, dan reset kata sandi.
- **Arsip Data** (admin) — data yang dihapus dapat dipulihkan atau dihapus permanen.

## Kebutuhan

- PHP 8.2+
- Composer
- Node.js 20+ & npm
- MySQL/MariaDB (atau SQLite untuk pengembangan)

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
# sesuaikan kredensial database pada .env
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

## Akun Default (seeder)

| Email                | Password | Role  |
| -------------------- | -------- | ----- |
| test@example.com     | password | admin |

> Segera ubah kata sandi/akun default sebelum digunakan di lingkungan produksi.

## Keamanan

- Rate limit login 5 percobaan / menit per email + IP.
- Otorisasi berbasis Policy untuk setiap aksi create/update/delete.
- Validasi input ketat (`exists`, batas nominal, format angka).
- Pengeluaran tidak boleh melebihi sisa pagu pos maupun sisa dana tahap.
- Data keuangan memakai soft delete (tidak terhapus permanen).
- Security header (CSP, X-Frame-Options, nosniff, Referrer-Policy) via middleware.
- HTTPS dipaksa otomatis saat `APP_ENV=production`.

## Pengujian

```bash
php artisan test
```

## Struktur Singkat

- `app/Http/Controllers` — Auth, Category (Monev & Dana), Transaction, ActivityLog, User, Trash.
- `app/Policies` — otorisasi role.
- `app/Models` — User, Category, BudgetStage, Transaction, ActivityLog.
- `app/Http/Middleware/SecurityHeaders.php` — header keamanan.

## Lisensi

Perangkat lunak ini menggunakan lisensi MIT.
