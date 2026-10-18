<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class BackupDatabase extends Command
{
    protected $signature = 'keuangan:backup {--keep= : Retensi cadangan dalam hari}';

    protected $description = 'Membuat cadangan database dan membersihkan cadangan lama';

    public function handle(): int
    {
        $path = config('keuangan.backup.path');

        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $driver = config('database.default');
        $connection = (array) config("database.connections.$driver");
        $timestamp = now()->format('Ymd-His');

        try {
            $file = match ($driver) {
                'sqlite' => $this->backupSqlite($connection, $path, $timestamp),
                'mysql', 'mariadb' => $this->backupMysql($connection, $path, $timestamp),
                default => null,
            };
        } catch (Throwable $e) {
            $this->error('Gagal membuat cadangan: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($file === null) {
            $this->error("Driver database '$driver' belum didukung untuk pencadangan.");

            return self::FAILURE;
        }

        $this->info('Cadangan dibuat: '.$file);

        $keep = (int) ($this->option('keep') ?? config('keuangan.backup.keep_days'));
        $removed = $this->prune($path, $keep);

        if ($removed > 0) {
            $this->info("Menghapus $removed cadangan lama (lebih dari $keep hari).");
        }

        return self::SUCCESS;
    }

    private function backupSqlite(array $connection, string $path, string $timestamp): string
    {
        $database = $connection['database'] ?? '';

        if ($database === ':memory:' || ! is_file($database)) {
            throw new RuntimeException('Berkas database SQLite tidak ditemukan: '.$database);
        }

        $target = $path.DIRECTORY_SEPARATOR."backup-$timestamp.sqlite";
        copy($database, $target);

        return $target;
    }

    private function backupMysql(array $connection, string $path, string $timestamp): string
    {
        $target = $path.DIRECTORY_SEPARATOR."backup-$timestamp.sql";

        $args = [
            'mysqldump',
            '-h', (string) ($connection['host'] ?? '127.0.0.1'),
            '-P', (string) ($connection['port'] ?? '3306'),
            '-u', (string) ($connection['username'] ?? 'root'),
        ];

        if (! empty($connection['password'])) {
            $args[] = '-p'.$connection['password'];
        }

        $args[] = '--result-file='.$target;
        $args[] = (string) $connection['database'];

        $process = new Process($args);
        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful() || ! is_file($target)) {
            throw new RuntimeException(trim($process->getErrorOutput()) ?: 'mysqldump gagal dijalankan.');
        }

        return $target;
    }

    private function prune(string $path, int $keepDays): int
    {
        $threshold = now()->subDays(max($keepDays, 1))->getTimestamp();
        $removed = 0;

        foreach (glob($path.DIRECTORY_SEPARATOR.'backup-*') ?: [] as $file) {
            if (is_file($file) && filemtime($file) < $threshold) {
                unlink($file);
                $removed++;
            }
        }

        return $removed;
    }
}
