# Sistem Keuangan Kecamatan Nyuatan

Aplikasi web monitoring & evaluasi (Monev) anggaran serta pencatatan transaksi keuangan Kantor Kecamatan Nyuatan, Kabupaten Kutai Barat. Dibangun dengan Laravel 12 + Blade + Bootstrap.

## Fitur

- **Autentikasi** dengan pembatasan percobaan login (anti brute-force).
- **Role pengguna**: `admin` (akses penuh), `bendahara` (input/ubah data), `viewer` (hanya lihat).
- **Monitoring & Evaluasi Anggaran** — pagu kantor, serapan, sisa alokasi, realisasi per pos.
- **Pencairan Dana (UP/GU)** — input dan hapus tahapan dana.
- **Transaksi Harian** — input, ubah, hapus (soft delete) dengan validasi pagu di sisi server.
- **Verifikasi Transaksi** — input bendahara berstatus *draft* menunggu persetujuan admin; hanya transaksi *disetujui* yang dihitung sebagai realisasi.
- **Cetak Laporan PDF** (dompdf) dengan filter periode, kata kunci, dan sumber dana.
- **Ekspor Excel/CSV** — unduh laporan transaksi sesuai filter aktif.
- **Laporan Anggaran per Pos** (PDF/CSV) — rekapitulasi pagu, realisasi, dan sisa tiap pos.
- **Grafik Tren Realisasi** bulanan pada halaman Monev.
- **Riwayat Aktivitas** (audit trail) — mencatat login, logout, dan perubahan data beserta IP, dengan filter pengguna/aksi/tabel/tanggal.
- **Manajemen Pengguna** (admin) — tambah/ubah/hapus akun, atur role, dan reset kata sandi.
- **Arsip Data** (admin) — data yang dihapus dapat dipulihkan atau dihapus permanen.
- **Profil Saya** — setiap pengguna dapat mengubah nama, email, dan kata sandinya sendiri.
- **Antarmuka berbahasa Indonesia** (pesan validasi, autentikasi, paginasi).
- **Halaman error kustom** (403/404/500) berbahasa Indonesia.

## Kebutuhan

- PHP 8.2+
- Composer
- MySQL/MariaDB (atau SQLite untuk pengembangan)

> Aset frontend (Bootstrap, Font Awesome, Google Fonts) dimuat via CDN, sehingga Node.js/npm tidak diperlukan.

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
# sesuaikan kredensial database pada .env
php artisan migrate --seed
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
- Transaksi wajib diverifikasi admin sebelum masuk laporan/realisasi resmi.
- Security header (CSP, X-Frame-Options, nosniff, Referrer-Policy) via middleware.
- HTTPS dipaksa otomatis saat `APP_ENV=production`.

## Pengujian

```bash
php artisan test
php vendor/bin/pint --test
```

## Deployment Produksi

1. Set `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://...`,
   `SESSION_SECURE_COOKIE=true`, dan kredensial database.
2. Cache konfigurasi & rute: `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
3. Jalankan migrasi: `php artisan migrate --force`.
4. Arahkan document root web server ke folder `public/` (bukan root proyek).
5. Pastikan HTTPS aktif — aplikasi memaksa skema HTTPS saat `APP_ENV=production`.
6. Jika di balik reverse-proxy/load balancer, `TrustProxies` sudah diaktifkan
   (`bootstrap/app.php`) agar IP klien & deteksi HTTPS benar.

## Backup

Data keuangan wajib di-backup berkala.

```bash
# Cadangan otomatis (SQLite: salin berkas; MySQL: mysqldump)
php artisan keuangan:backup
# retensi kustom (hari)
php artisan keuangan:backup --keep=30
```

Cadangan disimpan di `storage/app/backups` (ubah via `BACKUP_PATH`, retensi default
`BACKUP_KEEP_DAYS=14`). Jadwal harian pukul 01:00 sudah terdaftar; aktifkan di server dengan:

```bash
php artisan schedule:work            # cek manual saat pengembangan
* * * * * cd /path-proyek && php artisan schedule:run >> /dev/null 2>&1   # cron produksi
```

Untuk pemulihan manual (MySQL/MariaDB):

```bash
mysql -u USER -p NAMA_DB < backup-2026-01-01.sql
```

Simpan salinan di luar server dan uji proses pemulihan secara berkala.

## Data Contoh

Untuk mengisi dashboard, grafik, dan laporan dengan data demo (di luar produksi):

```bash
php artisan db:seed --class=DemoSeeder
```

## Struktur Singkat

- `app/Http/Controllers` — Auth, Category (Monev & Dana), Transaction, ActivityLog, User, Trash, Profile.
- `app/Console/Commands/BackupDatabase.php` — perintah `keuangan:backup`.
- `app/Policies` — otorisasi role.
- `app/Models` — User, Category, BudgetStage, Transaction, ActivityLog.
- `app/Http/Middleware/SecurityHeaders.php` — header keamanan.
- `resources/views/layouts/app.blade.php` — layout bersama (head, navbar, alert, formatter rupiah).
- `resources/views/partials` — alert & modal input dana bersama.
- `config/laporan.php` — kop instansi & data bendahara untuk laporan PDF.
- `config/keuangan.php` — lokasi & retensi pencadangan.
- `database/seeders/DemoSeeder.php` — data contoh untuk demo.

## Lisensi

Perangkat lunak ini menggunakan lisensi MIT.
