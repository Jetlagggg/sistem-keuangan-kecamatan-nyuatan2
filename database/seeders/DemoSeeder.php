<?php

namespace Database\Seeders;

use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        User::where('role', User::ROLE_ADMIN)->first() ?? User::factory()->admin()->create();
        User::where('role', User::ROLE_BENDAHARA)->first() ?? User::factory()->bendahara()->create();

        $tahun = now()->year;

        $stages = collect([
            ['nama_tahap' => 'UP', 'nominal' => 40_000_000, 'bulan' => 1],
            ['nama_tahap' => 'GU 1', 'nominal' => 60_000_000, 'bulan' => 4],
            ['nama_tahap' => 'GU 2', 'nominal' => 60_000_000, 'bulan' => 8],
        ])->map(fn (array $s) => BudgetStage::create([
            'nama_tahap' => $s['nama_tahap'],
            'nominal' => $s['nominal'],
            'tanggal_cair' => now()->setDate($tahun, $s['bulan'], 4),
            'keterangan' => 'Pencairan dana '.$s['nama_tahap'],
        ]));

        $posList = [
            'Belanja Alat Tulis Kantor',
            'Belanja Makan dan Minum',
            'Belanja Bahan Bakar',
            'Belanja Pemeliharaan',
            'Belanja Jasa Kebersihan',
            'Belanja Perjalanan Dinas',
            'Belanja Cetak dan Penggandaan',
            'Belanja Peralatan Kantor',
        ];

        $categories = collect($posList)->map(fn (string $nama) => Category::create([
            'nama' => $nama,
            'budget' => 15_000_000,
        ]));

        $penerima = ['CV Karya Mandiri', 'Toko Sumber Rejeki', 'PT Bangun Bersama', 'UD Sinar Abadi', 'Koperasi Karyawan'];
        $uraian = ['Pembelian rutin', 'Pembayaran tagihan', 'Pengadaan barang', 'Biaya operasional', 'Pemeliharaan ringan'];

        foreach ($categories as $category) {
            $jumlah = random_int(6, 10);

            for ($i = 0; $i < $jumlah; $i++) {
                $bulan = random_int(1, 12);
                $hari = random_int(1, 27);
                $tanggal = now()->setDate($tahun, $bulan, $hari)->toDateString();

                $roll = random_int(1, 100);
                $status = match (true) {
                    $roll <= 85 => Transaction::STATUS_DISETUJUI,
                    $roll <= 95 => Transaction::STATUS_DRAFT,
                    default => Transaction::STATUS_DITOLAK,
                };

                Transaction::create([
                    'tanggal' => $tanggal,
                    'category_id' => $category->id,
                    'budget_stage_id' => $stages->random()->id,
                    'nominal' => random_int(250_000, 1_200_000),
                    'penerima' => $penerima[array_rand($penerima)],
                    'keterangan' => $uraian[array_rand($uraian)],
                    'jenis' => 'keluar',
                    'status' => $status,
                    'verified_at' => $status === Transaction::STATUS_DRAFT ? null : now(),
                ]);
            }
        }
    }
}
