<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackupCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'keuangan-backup-'.uniqid();

        if (! is_dir($this->dir)) {
            mkdir($this->dir, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir.DIRECTORY_SEPARATOR.'*') ?: [] as $file) {
            is_file($file) && unlink($file);
        }

        is_dir($this->dir) && rmdir($this->dir);

        parent::tearDown();
    }

    public function test_backup_command_copies_sqlite_and_prunes_old_files(): void
    {
        $database = $this->dir.DIRECTORY_SEPARATOR.'source.sqlite';
        file_put_contents($database, 'SQLite format 3');

        config([
            'database.connections.sqlite.database' => $database,
            'keuangan.backup.path' => $this->dir,
            'keuangan.backup.keep_days' => 1,
        ]);

        $oldBackup = $this->dir.DIRECTORY_SEPARATOR.'backup-20200101-000000.sqlite';
        file_put_contents($oldBackup, 'lama');
        touch($oldBackup, now()->subDays(5)->getTimestamp());

        $this->artisan('keuangan:backup')->assertSuccessful();

        $this->assertFileDoesNotExist($oldBackup);

        $backups = glob($this->dir.DIRECTORY_SEPARATOR.'backup-*') ?: [];
        $this->assertCount(1, $backups);
        $this->assertSame('SQLite format 3', file_get_contents($backups[0]));
    }

    public function test_backup_command_fails_when_sqlite_file_missing(): void
    {
        config([
            'database.connections.sqlite.database' => $this->dir.DIRECTORY_SEPARATOR.'tidak-ada.sqlite',
            'keuangan.backup.path' => $this->dir,
        ]);

        $this->artisan('keuangan:backup')->assertFailed();
    }
}
