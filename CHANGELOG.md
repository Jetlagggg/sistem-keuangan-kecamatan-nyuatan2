# Changelog

Semua perubahan penting pada proyek ini dicatat di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/id/1.1.0/),
dan proyek ini menggunakan [Semantic Versioning](https://semver.org/lang/id/).

## [Unreleased] - 2026-10-22

### Ringkasan

Redesign visual "Buku Kas Kecamatan" untuk seluruh antarmuka: dari navbar gradien
dan kartu rounded menjadi lembar buku kas berbasis kertas, garis tipis, angka
tabular, dan aksen stempel merah. Struktur halaman, alur kerja, otorisasi, dan
fitur tetap dipertahankan.

### Ditambahkan

- **Sistem desain Buku Kas** di `public/css/buku-kas.css` (1.368 baris):
  token warna kertas/tinta, tipografi Public Sans + Roboto Mono, tombol, form,
  tabel buku kas, ringkasan pagu, tag status, masthead, footer, modal, halaman
  login/error, serta aturan responsif.
- **Dokumentasi desain** `DESIGN.md`: frontmatter token (41 warna - 22 tema
  siang dan 19 tema malam, 7 peran tipografi, radius, spacing, komponen) dan
  8 bagian sistem visual, termasuk North Star, aturan warna, tipografi,
  elevasi, komponen, dan do/don't.
- **Sidecar desain** `.impeccable/design.json` (schema v2): 8 komponen contoh,
  22 token warna siang dengan tonal ramp, tema malam di `extensions.themes`,
  metadata tipografi, shadow, motion, dan breakpoint.
- **Konteks produk** `PRODUCT.md`: platform, pengguna, tujuan, positioning,
  konteks operasional, batasan, prinsip produk, dan target aksesibilitas.
- **Dokumen brief dan surface**:
  `.impeccable/brief-buku-kas.md` dan
  `.impeccable/surfaces/resources-views-layouts-app-blade-php.md`.
- **Skip link** "Lewati ke konten" dan landmark `<main id="konten">` di layout.
- **Footer aplikasi** dengan identitas kantor dan tahun anggaran.
- **Pemilih tema Gelap/Terang** di masthead dan halaman login: tombol ikon
  dengan `aria-pressed`, label aksesibel, dan tooltip. Tanpa pilihan tersimpan,
  tema mengikuti preferensi sistem; pilihan pengguna disimpan di
  `localStorage` dengan kunci `buku-kas-theme`.
- **Skrip tema** `public/js/theme.js`: menerapkan tema sebelum halaman
  dirender, memperbarui ikon/label tombol, meta `theme-color`, dan mengikuti
  perubahan `prefers-color-scheme` selama pengguna belum memilih tema.
- **Tema "Buku Kas Malam"** di `public/css/buku-kas.css`: token kertas malam,
  tinta krem, garis redup, input malam, serta merah/hijau yang tetap terbaca
  sebagai sinyal status.
- **Label aksesibilitas** pada tombol navigasi mobile, tombol logout, dan
  elemen dekoratif (`aria-hidden`), serta `preconnect` untuk Google Fonts.
- **Test regresi tema** `tests/Feature/ThemeTest.php`: memastikan tombol tema
  tampil di login dan layout terautentikasi, serta skrip tema tersedia.
- **Ringkasan perubahan** ini di `CHANGELOG.md`.

### Diubah

- **Layout utama** `resources/views/layouts/app.blade.php`:
  - Google Fonts `Inter` diganti `Public Sans` (teks) dan `Roboto Mono`
    (angka, tanggal, label).
  - Blok `<style>` inline dihapus; seluruh gaya dimuat dari
    `public/css/buku-kas.css`.
  - Skrip tema dimuat di `<head>` dan meta `theme-color` ditambahkan agar tema
    tersimpan diterapkan sebelum halaman dirender.
  - Navbar gradien biru diganti masthead buku kas: lambang Kubar, nama kantor,
    subjudul, tahun anggaran, navigasi teks, aksi Input Dana (GU), tombol tema,
    profil pengguna beserta role, dan tombol logout.
  - Navigasi desktop memakai garis aktif merah; navigasi mobile memakai
    toggler Bootstrap dengan ikon dan label ARIA.
  - Tautan nav kini memakai teks (Transaksi, Anggaran, Riwayat, Pengguna,
    Arsip), bukan tombol outline.
- **Seluruh halaman aplikasi** memakai sistem visual baru:
  - `resources/views/auth/login.blade.php`: halaman login berbentuk lembar
    buku kas, logo Kubar, field dengan ikon, tombol masuk bergaya tinta, dan
    tombol tema di kanan atas.
  - `resources/views/transactions/index.blade.php`: ringkasan pagu/terserap/
    sisa, filter, tabel transaksi dengan saldo berjalan, state kosong, dan
    tampilan mobile.
  - `resources/views/categories/index.blade.php`: ringkasan anggaran, form pos,
    kartu pos kegiatan dengan indikator pagu, serta riwayat pencairan dana.
  - `resources/views/users/index.blade.php`: tabel pengguna, role/status, dan
    aksi manajemen pengguna.
  - `resources/views/arsip/index.blade.php`: tampilan arsip transaksi,
    kategori, dan tahap anggaran.
  - `resources/views/activity-logs/index.blade.php`: riwayat aktivitas dan
    metadata audit.
  - `resources/views/profile/edit.blade.php`: form profil dan kata sandi.
  - `resources/views/errors/403.blade.php`, `404.blade.php`, dan
    `500.blade.php`: halaman error yang konsisten dengan sistem buku kas dan
    tetap mengikuti tema tersimpan.
  - `resources/views/partials/alerts.blade.php`: alert sukses/error yang
    selaras dengan palet.
  - `resources/views/partials/modal-input-gu.blade.php`: modal Input Dana (GU)
    dengan header/footer bergaris dan field yang konsisten.
- **Laporan PDF** `resources/views/transactions/cetak.blade.php`: tipografi
  print-safe (DejaVu Sans/DejaVu Sans Mono), palet cetak, kop laporan, tabel,
  total, dan blok tanda tangan disesuaikan dengan sistem baru.
- **Logo masthead**: monogram teks "KN" diganti lambang Kubar dari
  `public/img/kubar.png`; aset logo yang sudah dipakai di halaman login kini
  juga dipakai di masthead aplikasi.
- **Urutan Riwayat Pencairan Dana (UP/GU)** di
  `app/Http/Controllers/CategoryController.php`: dari tanggal menurun menjadi
  kronologis naik (`tanggal_cair`, lalu `id`) agar urutan tahap konsisten.
- **Dokumentasi proyek**:
  - `README.md` mencantumkan stylesheet lokal `public/css/buku-kas.css`.
  - `AGENTS.md` mencantumkan stylesheet lokal, `DESIGN.md`, sidecar
    `.impeccable/design.json`, dan skrip tema `public/js/theme.js`.
  - `PRODUCT.md` diperbarui agar menyebut lambang Kubar, sistem desain baru,
    dan preferensi tema gelap/terang.
  - `DESIGN.md` menambahkan tema "Buku Kas Malam", token malam, dan aturan
    kontras/state untuk mode gelap.
- **Stylesheet** `public/css/buku-kas.css` sekarang memakai variabel untuk
  input, placeholder, hover, warna teks di atas tombol solid, arsir peringatan,
  bayangan modal, dan garis login; nilai-nilai tersebut memiliki padanan malam.

### Diperbaiki

- Identitas visual login dan aplikasi kini konsisten memakai lambang Kubar,
  bukan mark teks "KN" di masthead.
- Gaya lama berupa gradien, kartu rounded, shadow, dan warna biru Bootstrap
  diganti permukaan kertas datar dengan garis tipis dan aksen merah terkontrol.
- Angka uang, tanggal, dan label kolom kini memakai Roboto Mono dengan angka
  tabular agar sejajar saat dibaca menurun.
- Halaman error 403/404/500 tidak lagi tampil sebagai halaman polos; semuanya
  mengikuti sistem visual yang sama.
- Navigasi mobile mendapat label ARIA, dan tabel lebar dapat digeser
  horizontal tanpa memecahkan layout.
- Fokus keyboard terlihat pada link, tombol, dan field; skip link memungkinkan
  pengguna keyboard melewati masthead.
- Laporan cetak tidak lagi bergantung pada font web yang tidak tersedia saat
  PDF dibuat; font dan warna cetak kini eksplisit.

### Dihapus

- Blok CSS inline di `resources/views/layouts/app.blade.php`.
- Import Google Fonts `Inter` dan gaya navbar/card/summary lama.
- Monogram teks "KN" dari masthead.
- Scaffolding review sementara: route `/__review/*`, `.env.review`, skrip
  capture/build, dan database review sementara. Artefak ini tidak termasuk
  deliverable.

### Tidak Berubah

- Tidak ada perubahan skema database, migrasi, seeder, atau data produksi.
- Tidak ada perubahan route final; route review sementara sudah dihapus.
- Tidak ada perubahan dependensi Composer/npm; aplikasi tetap tanpa `@vite`
  dan tanpa langkah build frontend.
- Tidak ada perubahan aturan otorisasi, validasi anggaran, soft delete, atau
  audit trail.
- Test lain tidak diubah.

### Verifikasi

- `php artisan test`: **83 passed** (225 assertions).
- `php vendor/bin/pint --test`: **pass**.
- `node --check public/js/theme.js`: **pass**.
- `impeccable detect`: **0 warning**; tersisa advisory untuk ukuran font
  kecil/ramp cetak yang memang dipakai.
- Pemeriksaan browser: tema sistem gelap/terang terdeteksi, tombol mengubah
  tema dan menyimpan pilihan di `localStorage`, tidak ada error console, dan
  layout mobile 390px tidak overflow. Rasio kontras tema malam: tinta/kertas
  14,89:1; muted/sheet 5,93:1; merah/sheet 5,34:1; teks di atas stempel
  5,85:1; hijau/sheet 8,42:1.
- Review visual desktop dan mobile mencakup login, transaksi, anggaran,
  pengguna, arsip, riwayat aktivitas, profil, modal Input Dana (GU), halaman
  error, laporan PDF, serta tema gelap/terang pada login dan transaksi.
- Screenshot review tersedia di `.impeccable/review/` (artefak lokal, tidak
  termasuk kode aplikasi).
