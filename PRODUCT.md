# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Bendahara dan admin kantor kecamatan sebagai pengguna utama, bekerja di desktop kantor untuk input transaksi harian, memantau realisasi anggaran, dan mencetak laporan. Viewer (pimpinan/verifikator) memakai sesekali untuk membaca data. Tidak ada kebutuhan seluler yang dikonfirmasi.

## Product Purpose

Mencatat dan memantau keuangan Kecamatan Nyuatan: dana UP/GU yang masuk, pembagiannya ke pos kegiatan, dan pengeluaran harian, sehingga sisa anggaran per pos dan per tahap selalu diketahui. Sukses berarti bendahara dapat menyelesaikan pencatatan harian dan mencetak laporan tanpa selisih, dan pagu tidak pernah terlampaui.

## Positioning

Satu buku kas internal kantor yang menahan pengeluaran di server terhadap dua plafon sekaligus — sisa pagu pos dan sisa dana tahap GU — serta menyimpan jejak audit dan arsip, sehingga angka monev selalu konsisten dengan transaksi.

## Operating Context

- Dipakai di kantor kecamatan, desktop, Bahasa Indonesia.
- Alur kerja: input dana UP/GU, bagi ke pos kegiatan, catat transaksi harian, pantau monev, cetak laporan PDF.
- Peran: admin (penuh), bendahara (buat/ubah), viewer (baca).
- Nominal rupiah bulat; tanggal mengikuti zona Asia/Makassar.
- Preferensi tampilan gelap/terang dapat diganti dari masthead atau login, diingat per browser, dan default mengikuti preferensi sistem.
- Data yang dihapus diarsipkan (soft delete) dan dapat dipulihkan admin.

## Capabilities and Constraints

- Fitur: monev anggaran, pos kegiatan, dana UP/GU, transaksi harian dengan filter, cetak PDF, riwayat aktivitas, manajemen pengguna, arsip.
- Batas anggaran divalidasi di server; pengeluaran tidak boleh melebihi sisa pos maupun sisa tahap.
- Stack Laravel 12 + Blade; aset frontend dimuat via CDN.
- Ruang lingkup desain ini: visual saja — struktur halaman, navigasi, alur, dan fitur dipertahankan.
- Lambang Kubar tersedia di `public/img/kubar.png` dan dipakai di login serta masthead; tampilan harus tetap terasa formal pemerintahan.

## Brand Commitments

Nama "Kantor Kecamatan Nyuatan", lambang Kubar, dan istilah resmi (UP, GU, pagu, pos kegiatan, realisasi) dipertahankan. Palet dan tipografi baru mengikuti `DESIGN.md`. Nada: formal, tenang, dapat dipercaya.

## Evidence on Hand

Aplikasi Laravel yang berjalan dengan tampilan Blade/Bootstrap saat ini; laporan PDF; data contoh lewat seeder. Tidak ada testimoni, studi kasus, atau aset fotografi; jangan mengarang.

## Product Principles

1. Angka dulu: sisa pagu, realisasi, dan status batas terbaca dalam sekali pandang.
2. Tidak ada kejutan: pola dan label yang dikenal bendahara dipertahankan; perubahan bersifat visual.
3. Tenang dan resmi: kepadatan data tinggi tanpa terasa ramai.
4. Aksi berisiko (hapus, arsip) harus jelas dan sulit tersentuh tanpa sengaja.
5. Konsisten di semua halaman: satu sistem visual, bukan hiasan per halaman.

## Accessibility & Inclusion

Belum ada standar khusus yang ditetapkan. Target kerja: kontras teks WCAG AA, fokus terlihat, dan tabel tetap terbaca pada layar 1366×768.
