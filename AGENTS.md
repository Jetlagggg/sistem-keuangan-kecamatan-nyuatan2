# AGENTS.md

Panduan singkat untuk agen/developer yang bekerja di repo ini.

## Ringkasan Proyek

Sistem Keuangan Kecamatan Nyuatan — aplikasi Laravel 12 (PHP ^8.2) berbasis Blade +
Bootstrap untuk monev anggaran dan pencatatan transaksi keuangan.

- Aset frontend dimuat via **CDN** (Bootstrap, Font Awesome, Google Fonts) dengan SRI.
  Tampilan **tidak** memakai `@vite`, jadi `npm install`/`npm run build` tidak wajib
  untuk menjalankan aplikasi.
- Zona waktu aplikasi: `Asia/Makassar` (lihat `config/app.php`).

## Perintah Penting

```bash
# Instalasi
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Menjalankan aplikasi
php artisan serve

# Pengujian (wajib sebelum commit)
php artisan test
# atau
composer test

# Lint / format kode PHP (Laravel Pint, preset "laravel")
php vendor/bin/pint          # perbaiki otomatis
php vendor/bin/pint --test   # cek tanpa mengubah (dipakai CI)

# Cadangan database (SQLite: salin berkas, MySQL: mysqldump) + retensi otomatis
php artisan keuangan:backup --keep=14

# Data contoh (di luar produksi)
php artisan db:seed --class=DemoSeeder
```

Pengujian memakai SQLite in-memory (`phpunit.xml`), jadi tidak perlu database untuk `php artisan test`.

## Konvensi

- Ikuti gaya Laravel Pint; jalankan `pint` sebelum commit.
- Jangan tambahkan komentar kecuali diminta.
- Validasi input di controller dengan `$request->validate([...])`; parsing nominal memakai
  helper `parseNominal()`/`parseAmount()` (hapus non-digit).
- Uang disimpan sebagai integer rupiah penuh (tanpa desimal).

## Arsitektur & Aturan Penting

- **Role pengguna** (`users.role`): `admin` (penuh), `bendahara` (buat/ubah), `viewer` (read-only).
  Otorisasi lewat Policy di `app/Policies` (`create`/`update` = `canManage()`, `delete` = `isAdmin()`).
- **Soft delete** aktif pada `Transaction`, `Category`, `BudgetStage` (`deleted_at`).
  Hapus = arsip; query default mengabaikan baris terhapus. Pemulihan/hapus permanen
  (admin) lewat `TrashController` (`/arsip`).
- **Manajemen pengguna** khusus admin (`UserPolicy`, `UserController`); admin terakhir
  tidak bisa dihapus atau diturunkan role-nya.
- **Batas anggaran** dijaga di server oleh `TransactionController::guardBudget()` di dalam
  `DB::transaction` + `lockForUpdate` (aman dari input bersamaan): pengeluaran tidak boleh
  melebihi sisa pagu pos maupun sisa dana tahap.
- **Status & verifikasi transaksi** (`transactions.status`): `draft`, `disetujui`, `ditolak`.
  Input bendahara → `draft`; input admin → otomatis `disetujui`. Admin menyetujui/menolak lewat
  `transactions.verify` / `transactions.reject` (`TransactionPolicy::verify` = admin). Hanya
  transaksi `disetujui` (scope `approved`) yang dihitung sebagai realisasi dan tampil di laporan
  PDF/CSV; `guardBudget` tetap menghitung semua status agar pagu tidak terlampaui oleh draft.
- **Bahasa**: locale `id` (`lang/id`), pesan validasi/auth/paginasi berbahasa Indonesia.
- **Audit trail**: setiap perubahan memanggil `ActivityLog::record(...)`.
- **Pencadangan**: `app/Console/Commands/BackupDatabase.php` (`keuangan:backup`) menyalin
  database ke `storage/app/backups` (konfigurasi `config/keuangan.php`); dijadwalkan harian
  di `routes/console.php`.
- **Keamanan**: middleware `App\Http\Middleware\SecurityHeaders` menambahkan CSP & header
  keamanan ke semua respons; login dibatasi rate limit. HTTPS dipaksa saat `APP_ENV=production`.
- Migrasi baru diberi prefix tanggal; hindari mengubah migrasi yang sudah dijalankan di produksi
  selain perbaikan format.

## CI

`.github/workflows/tests.yml` menjalankan `./vendor/bin/pint --test` dan `php artisan test`
pada setiap push/PR.
